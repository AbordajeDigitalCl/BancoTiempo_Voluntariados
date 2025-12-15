import React, { useState, useEffect, useRef } from "react";
import ServiceCard from "./ServicioProductoCard";

export default function ServiceCardListview() {
  const [items, setItems] = useState(Array.from({ length: 10 }, (_, i) => i));
  const loader = useRef(null);

  const loadMore = () => {
    setTimeout(() => {
      setItems(prev => [...prev, ...Array.from({ length: 10 }, (_, i) => prev.length + i)]);
    }, 1000);
  };

  useEffect(() => {
    const observer = new IntersectionObserver(
      entries => {
        if (entries[0].isIntersecting) {
          loadMore();
        }
      },
      { threshold: 1.0 }
    );

    if (loader.current) {
      observer.observe(loader.current);
    }

    return () => {
      if (loader.current) observer.unobserve(loader.current);
    };
  }, []);

  return (
    <div className="w-full flex flex-col items-center">
      <div className="w-full max-w-5xl px-4 space-y-6">
        {items.map(id => (
          <ServiceCard key={id} />
        ))}
      </div>
      <div ref={loader} className="h-10 flex justify-center items-center text-gray-500">
        Cargando más...
      </div>
    </div>
  );
}