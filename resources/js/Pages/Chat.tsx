import React from "react";

const people = [
  {
    id: 1,
    image: "https://picsum.photos/100",
    date: "19/05/2025",
    name: "John Doe",
    description: "Descripción breve en gris."
  },
  {
    id: 2,
    image: "https://picsum.photos/100",
    date: "18/05/2025",
    name: "Jane Smith",
    description: "Otra descripción gris."
  }
  // Agrega más objetos según sea necesario
];

export default function SidebarWithContent() {
  return (
    <div className="grid grid-cols-[max-content_1fr] grid-rows-[auto_1fr_auto] h-screen w-screen bg-gray-100 overflow-hidden">
      {/* Sidebar */}
      <aside className="col-start-1 row-span-3 w-full max-w-xs bg-white p-4 overflow-hidden">
        <div className="h-full pr-2 overflow-y-scroll scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 hover:scrollbar-thumb-gray-600 scrollbar-thumb-rounded-full scrollbar-track-rounded-full">
          <div className="space-y-4">
            {people.map((person) => (
              <div
                key={person.id}
                className="flex bg-white shadow rounded-lg overflow-hidden"
              >
                <img
                  src={person.image}
                  alt={person.name}
                  className="w-1/3 object-cover aspect-square"
                />
                <div className="flex flex-col justify-between p-2 w-2/3">
                  <div className="flex justify-between text-sm text-gray-500">
                    <span></span>
                    <span>{person.date}</span>
                  </div>
                  <div className="text-xl font-bold">{person.name}</div>
                  <div className="text-gray-500 text-sm">
                    {person.description}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </aside>

      {/* Top Card (modified layout) */}
      <div className="col-start-2 row-start-1 flex w-full h-36 mb-0">
        <img
          src="https://picsum.photos/100"
          alt="Preview"
          className="w-1/6 object-cover aspect-square"
        />
        <div className="flex flex-col justify-between pl-4 pr-2 w-5/6">
          <div className="pt-4 text-4xl font-bold text-left">John Doe</div>
          <div className="text-gray-500 text-base text-left pb-2">Vista previa sin fecha</div>
        </div>
      </div>

      {/* Scrollable Message Area */}
      <main className="col-start-2 row-start-2 overflow-hidden">
        <div className="h-full overflow-y-scroll space-y-4 pr-2 mb-4 scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-200 hover:scrollbar-thumb-gray-600 scrollbar-thumb-rounded-full scrollbar-track-rounded-full">
          {/* Left aligned message */}
          <div className="max-w-2/3 w-2/3 bg-gray-300 text-black p-3 rounded-lg">
            Cámara, ya quedó tu nave, ya nomás pasa por ella el domingo que ando libre.
          </div>
          <div className="text-gray-500 text-sm">19/05/2025 03:45 pm</div>

          {/* Right aligned message */}
          <div className="flex justify-end">
            <div className="max-w-2/3 w-2/3 bg-blue-500 text-white p-3 rounded-lg text-right">
              ¡Perfecto! Allí estaré, muchas gracias.
            </div>
          </div>
          <div className="flex justify-end text-gray-500 text-sm">19/05/2025 04:10 pm</div>
        </div>
      </main>

      {/* Bottom Textarea & Buttons */}
      <div className="col-start-2 row-start-3 bg-white px-4 py-3 flex items-end w-full">
        <textarea
          placeholder="Escribe algo..."
          className="flex-1 resize-none p-2 rounded-md border border-gray-300 h-20"
        ></textarea>
        <div className="flex flex-col space-y-2 ml-4 items-center h-full justify-end">
          <button className="flex flex-col items-center justify-center rounded-full bg-black text-white px-2 py-0.5 text-xs h-10">
            <img
              src="https://cdn-icons-png.flaticon.com/512/847/847969.png"
              alt="Imagen"
              className="w-4 h-4 mb-0.5"
            />
            <span className="text-[10px] leading-tight">Imagen</span>
          </button>
          <button className="rounded-full bg-white text-black border border-black px-4 py-1">
            Enviar
          </button>
        </div>
      </div>
    </div>
  );
}
