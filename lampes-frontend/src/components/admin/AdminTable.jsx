import React from 'react'
import './AdminUi.css'

const AdminTable = ({ columns, rows, emptyMessage = 'Aucune donnee disponible.' }) => (
  <div className="admin-table-wrap">
    <table className="admin-table">
      <thead>
        <tr>
          {columns.map((column) => (
            <th key={column.key}>{column.label}</th>
          ))}
        </tr>
      </thead>
      <tbody>
        {rows.length === 0 ? (
          <tr>
            <td colSpan={columns.length} className="admin-empty">{emptyMessage}</td>
          </tr>
        ) : rows.map((row) => (
          <tr key={row.id} className="admin-table-row">
            {columns.map((column) => (
              <td key={`${row.id}-${column.key}`}>
                {column.render ? column.render(row) : row[column.key]}
              </td>
            ))}
          </tr>
        ))}
      </tbody>
    </table>
  </div>
)

export default AdminTable
