from __future__ import annotations

import argparse
import json
import re
import zipfile
from dataclasses import dataclass, field
from pathlib import Path
from typing import Iterable
import xml.etree.ElementTree as ET


WORD_NS = {
    "w": "http://schemas.openxmlformats.org/wordprocessingml/2006/main",
    "r": "http://schemas.openxmlformats.org/officeDocument/2006/relationships",
    "a": "http://schemas.openxmlformats.org/drawingml/2006/main",
}

SPEC_LABELS = [
    "Puissance",
    "Flux lumineux",
    "Lumens",
    "Température de couleur",
    "Angle d’éclairage",
    "Angle d'eclairage",
    "Matière",
    "Matiere",
    "Type de LED",
    "Batteries",
    "Batterie",
    "Capacité",
    "Capacite",
    "Détection",
    "Detection",
    "Angle détection",
    "Angle detection",
    "Distance détection",
    "Distance detection",
    "Protection",
    "Protection IP",
    "Durée de vie",
    "Duree de vie",
    "Veilleuse nocturne",
    "Autonomie",
    "Batterie remplaçable",
    "Batterie remplacable",
    "Panneau photovoltaïque",
    "Panneau photovoltaique",
    "Longueur de câble",
    "Longueur de cable",
    "Montage",
    "Finition",
    "Poids",
    "Dimensions",
]

CATEGORY_NORMALIZATION = {
    "applique solaire": "Applique solaire",
    "projecteur solaire": "Projecteur solaire",
    "spots encastrable solaires": "Spots encastrables solaires",
    "spot encastrable solaire": "Spots encastrables solaires",
    "born& potelet solaire": "Bornes et potelets solaires",
    "bornes & potelets solaires": "Bornes et potelets solaires",
    "piquet solaire": "Piquets solaires",
    "lampadaire solaire": "Lampadaires solaires",
    "guirlande solaire": "Guirlandes solaires",
    "kit photovoltaïque d’autoconsommation plug&play": "Kits photovoltaïques d'autoconsommation PLUG&PLAY",
    "kit photovoltaique d’autoconsommation plug&play": "Kits photovoltaïques d'autoconsommation PLUG&PLAY",
    "kit photovoltaïque d'autoconsommation plug&play": "Kits photovoltaïques d'autoconsommation PLUG&PLAY",
    "kit photovoltaique d'autoconsommation plug&play": "Kits photovoltaïques d'autoconsommation PLUG&PLAY",
}

SPEC_LABEL_PATTERN = re.compile(
    r"(" + "|".join(re.escape(label) for label in SPEC_LABELS) + r")\s*:\s*",
    re.IGNORECASE,
)
CATEGORY_PATTERN = re.compile(r"^cat\S*\s*\d+\s*:?\s*(.+)$", re.IGNORECASE)
NAME_PATTERN = re.compile(r"^nom\s*:?\s*(.+)$", re.IGNORECASE)
PRICE_PATTERN = re.compile(r"prix\s*:?\s*(\d+(?:[.,]\d+)?)", re.IGNORECASE)
SKU_PATTERN = re.compile(r"\bUGS\b", re.IGNORECASE)
NOISE_PATTERN = re.compile(
    r"^(produits similaires|categories?|caracteristiques?|caracteistique|caracteritique|caractesistiwue|caracteristique)\b",
    re.IGNORECASE,
)


@dataclass
class ParagraphItem:
    text: str
    images: list[str]


@dataclass
class TableItem:
    rows: list[list[str]]


@dataclass
class ProductDraft:
    category: str | None
    name: str
    price: float | None = None
    descriptions: list[str] = field(default_factory=list)
    specs: dict[str, str] = field(default_factory=dict)
    image_candidates: list[str] = field(default_factory=list)

    def add_description(self, text: str) -> None:
        cleaned = cleanup_text(text)
        if not cleaned:
            return
        if NOISE_PATTERN.match(cleaned):
            return

        if SPEC_LABEL_PATTERN.search(cleaned):
            for key, value in extract_inline_specs(cleaned).items():
                self.specs.setdefault(key, value)

        if cleaned not in self.descriptions:
            self.descriptions.append(cleaned)

    def add_table(self, rows: list[list[str]]) -> None:
        for row in rows:
            if len(row) < 2:
                continue
            key = cleanup_spec_key(row[0])
            value = cleanup_text(row[1])
            if key and value:
                self.specs[key] = value

    def finalize(self, media_sizes: dict[str, int]) -> dict[str, object]:
        description = "\n\n".join(self.descriptions).strip()
        category = normalize_category(self.category)
        specs = dict(self.specs)
        gallery_images = choose_gallery_images(self.image_candidates, media_sizes)
        image = gallery_images[0] if gallery_images else None

        if not description:
            description = f"{self.name} est un produit solaire de la categorie {category.lower()}."

        if not specs:
            specs = {"details": "Caracteristiques indisponibles dans le document source."}

        return {
            "category": category,
            "category_description": build_category_description(category),
            "name": cleanup_name(self.name),
            "price": self.price if self.price is not None else 0,
            "description": description,
            "short_description": cleanup_text(description)[:180].rstrip(),
            "image": image,
            "image_url": image,
            "gallery_images": gallery_images,
            "stock": 10,
            "status": "active",
            "specifications": specs,
        }


def cleanup_text(value: str) -> str:
    value = value.replace("\xa0", " ")
    value = re.sub(r"\s+", " ", value)
    return value.strip()


def cleanup_name(value: str) -> str:
    value = cleanup_text(value).lstrip(": ").strip()
    return value


def cleanup_spec_key(value: str) -> str:
    normalized = cleanup_text(value).strip(" .:-")
    lower = normalized.lower()
    replacements = {
        "flux lumineux": "Lumens",
        "angle d'eclairage": "Angle d’éclairage",
        "matiere": "Matière",
        "batteries": "Batterie",
        "capacite": "Capacité",
        "detection": "Détection",
        "angle detection": "Angle détection",
        "distance detection": "Distance détection",
        "duree de vie": "Durée de vie",
        "batterie remplacable": "Batterie remplaçable",
        "panneau photovoltaique": "Panneau photovoltaïque",
        "longueur de cable": "Longueur de câble",
    }
    return replacements.get(lower, normalized)


def normalize_category(value: str | None) -> str:
    if not value:
        return "Catalogue solaire"
    cleaned = cleanup_text(value)
    cleaned = cleaned.replace("Categorie", "Catégorie")
    key = cleaned.lower()
    return CATEGORY_NORMALIZATION.get(key, cleaned)


def build_category_description(category: str) -> str:
    return f"Selection importee depuis le catalogue Word fourni pour la categorie {category}."


def sanitize_spec_value(value: str) -> str:
    value = cleanup_text(value)
    for marker in [
        "Le conseil de l’équipe",
        "Le conseil de l'equipe",
        "Dans le même esprit",
        "Dans le meme esprit",
        "Découvrez notre large gamme",
        "Decouvrez notre large gamme",
        "UGS",
    ]:
        if marker in value:
            value = value.split(marker, 1)[0].strip(" .|-")
    return value


def extract_inline_specs(text: str) -> dict[str, str]:
    matches = list(SPEC_LABEL_PATTERN.finditer(text))
    if not matches:
        return {}

    specs: dict[str, str] = {}
    for index, match in enumerate(matches):
        key = cleanup_spec_key(match.group(1))
        start = match.end()
        end = matches[index + 1].start() if index + 1 < len(matches) else len(text)
        value = sanitize_spec_value(text[start:end].strip(" .|-"))
        if value:
            specs[key] = value
    return specs


def choose_best_image(candidates: Iterable[str], media_sizes: dict[str, int]) -> str | None:
    gallery = choose_gallery_images(candidates, media_sizes)
    return gallery[0] if gallery else None


def choose_gallery_images(candidates: Iterable[str], media_sizes: dict[str, int]) -> list[str]:
    filtered = [candidate for candidate in candidates if candidate]
    if not filtered:
        return []

    unique_candidates = list(dict.fromkeys(filtered))
    ordered_candidates = sorted(
        unique_candidates,
        key=lambda candidate: (
            0 if Path(candidate).suffix.lower() == ".png" else 1,
            -media_sizes.get(candidate, 0),
            unique_candidates.index(candidate),
        ),
    )

    gallery: list[str] = []
    for candidate in ordered_candidates:
        extension = Path(candidate).suffix.lower()
        gallery.append(f"/catalog-import/{Path(candidate).stem}{extension}")

    return gallery


def parse_document(source_path: Path) -> tuple[list[ParagraphItem | TableItem], dict[str, str], dict[str, int], zipfile.ZipFile]:
    archive = zipfile.ZipFile(source_path)
    rels = ET.fromstring(archive.read("word/_rels/document.xml.rels"))
    relmap = {rel.attrib["Id"]: rel.attrib["Target"] for rel in rels}
    media_sizes = {
        info.filename.replace("word/", ""): info.file_size
        for info in archive.infolist()
        if info.filename.startswith("word/media/")
    }

    root = ET.fromstring(archive.read("word/document.xml"))
    body = root.find("w:body", WORD_NS)
    if body is None:
        raise RuntimeError("Le document Word ne contient pas de corps lisible.")

    items: list[ParagraphItem | TableItem] = []

    for child in body:
        tag = child.tag.rsplit("}", 1)[-1]
        if tag == "p":
            texts = [node.text or "" for node in child.findall(".//w:t", WORD_NS)]
            text = cleanup_text("".join(texts))
            images = []
            for blip in child.findall(".//a:blip", WORD_NS):
                rid = blip.attrib.get(f"{{{WORD_NS['r']}}}embed")
                target = relmap.get(rid or "")
                if target and target.startswith("media/"):
                    images.append(target)
            if text or images:
                items.append(ParagraphItem(text=text, images=images))
        elif tag == "tbl":
            rows: list[list[str]] = []
            for tr in child.findall("w:tr", WORD_NS):
                cells = []
                for tc in tr.findall("w:tc", WORD_NS):
                    texts = [node.text or "" for node in tc.findall(".//w:t", WORD_NS)]
                    cells.append(cleanup_text("".join(texts)))
                if any(cells):
                    rows.append(cells)
            if rows:
                items.append(TableItem(rows=rows))

    return items, relmap, media_sizes, archive


def build_products(items: list[ParagraphItem | TableItem], media_sizes: dict[str, int]) -> list[dict[str, object]]:
    current_category: str | None = None
    current_product: ProductDraft | None = None
    products: list[dict[str, object]] = []
    pending_price: float | None = None

    for item in items:
        if isinstance(item, ParagraphItem):
            category_match = CATEGORY_PATTERN.match(item.text)
            if category_match:
                if current_product is not None:
                    products.append(current_product.finalize(media_sizes))
                    current_product = None
                current_category = category_match.group(1).strip()
                continue

            name_match = NAME_PATTERN.match(item.text)
            if name_match:
                if current_product is not None:
                    products.append(current_product.finalize(media_sizes))
                current_product = ProductDraft(
                    category=current_category,
                    name=cleanup_name(name_match.group(1)),
                    price=pending_price,
                )
                pending_price = None
                continue

            price_match = PRICE_PATTERN.search(item.text)
            if current_product is None and price_match:
                pending_price = float(price_match.group(1).replace(",", "."))
                continue

            if current_product is None:
                continue

            if item.images:
                current_product.image_candidates.extend(item.images)

            if item.images and not item.text:
                continue

            if price_match and current_product.price is None:
                current_product.price = float(price_match.group(1).replace(",", "."))
                continue

            if price_match and cleanup_text(item.text).lower().startswith("prix"):
                pending_price = float(price_match.group(1).replace(",", "."))
                continue

            if NOISE_PATTERN.match(item.text):
                continue

            for key, value in extract_inline_specs(item.text).items():
                current_product.specs.setdefault(key, value)

            current_product.add_description(item.text)

        elif current_product is not None:
            current_product.add_table(item.rows)

    if current_product is not None:
        products.append(current_product.finalize(media_sizes))

    return products


def export_images(archive: zipfile.ZipFile, output_dir: Path, image_paths: Iterable[str]) -> None:
    output_dir.mkdir(parents=True, exist_ok=True)
    for child in output_dir.iterdir():
        if child.is_file():
            child.unlink()

    for image in sorted(set(image_paths)):
        source = f"word/{image}"
        if source not in archive.namelist():
            continue
        data = archive.read(source)
        target = output_dir / Path(image).name
        target.write_bytes(data)


def write_json_dataset(products: list[dict[str, object]], destination: Path) -> None:
    destination.parent.mkdir(parents=True, exist_ok=True)
    payload = json.dumps(products, ensure_ascii=False, indent=2)
    destination.write_text(payload + "\n", encoding="utf-8")


def main() -> None:
    parser = argparse.ArgumentParser(description="Importe un catalogue Word .docx vers les donnees du backend.")
    parser.add_argument("source", help="Chemin vers le document Word source")
    parser.add_argument(
        "--backend-root",
        default=str(Path(__file__).resolve().parents[1]),
        help="Racine du backend Laravel",
    )
    args = parser.parse_args()

    source_path = Path(args.source).resolve()
    backend_root = Path(args.backend_root).resolve()
    data_dir = backend_root / "database" / "data"
    data_file = data_dir / "solar4life_products.json"
    legacy_php_file = data_dir / "solar4life_products.php"
    image_dir = backend_root / "public" / "catalog-import"

    items, _rels, media_sizes, archive = parse_document(source_path)
    try:
        products = build_products(items, media_sizes)
        used_images = []
        for product in products:
            for image in product.get("gallery_images", []):
                if not isinstance(image, str) or not image:
                    continue
                used_images.append(f"media/{Path(image).name}")

            image = product.get("image")
            if isinstance(image, str) and image:
                used_images.append(f"media/{Path(image).name}")

        export_images(archive, image_dir, used_images)
        write_json_dataset(products, data_file)
        if legacy_php_file.exists():
            legacy_php_file.unlink()
    finally:
        archive.close()

    categories = sorted({product["category"] for product in products})
    print(f"Produits importes: {len(products)}")
    print("Categories:")
    for category in categories:
        print(f"- {category}")
    print(f"Fichier genere: {data_file}")
    print(f"Images extraites: {len(used_images)} dans {image_dir}")


if __name__ == "__main__":
    main()
