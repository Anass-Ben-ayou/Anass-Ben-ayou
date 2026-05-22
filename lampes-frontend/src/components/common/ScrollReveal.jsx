import React, { useEffect, useRef, useState } from 'react'

const ScrollReveal = ({
  as: Component = 'div',
  children,
  className = '',
  delay = 0,
  direction = 'up',
  once = true,
  style,
  ...props
}) => {
  const elementRef = useRef(null)
  const [isVisible, setIsVisible] = useState(false)

  useEffect(() => {
    const element = elementRef.current

    if (!element || typeof IntersectionObserver === 'undefined') {
      setIsVisible(true)
      return undefined
    }

    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsVisible(true)

          if (once) {
            observer.unobserve(entry.target)
          }
        } else if (!once) {
          setIsVisible(false)
        }
      },
      {
        rootMargin: '0px 0px -80px',
        threshold: 0.12
      }
    )

    observer.observe(element)

    return () => {
      observer.disconnect()
    }
  }, [once])

  return (
    <Component
      ref={elementRef}
      className={[
        'scroll-reveal',
        `scroll-reveal-${direction}`,
        isVisible ? 'is-visible' : '',
        className
      ].filter(Boolean).join(' ')}
      style={{
        ...style,
        '--reveal-delay': `${delay}ms`
      }}
      {...props}
    >
      {children}
    </Component>
  )
}

export default ScrollReveal
