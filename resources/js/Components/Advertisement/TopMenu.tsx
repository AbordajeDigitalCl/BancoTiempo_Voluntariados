import { useEffect, useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';

export default function TopMenu({ userName }: { userName: string }) {
  const [open, setOpen] = useState(false);
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (ref.current && !ref.current.contains(event.target as Node)) {
        setOpen(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="relative" ref={ref}>
      <button
        type="button"
        onClick={() => setOpen(!open)}
        className="w-9 h-9 rounded-lg border border-gray-300 bg-white flex items-center justify-center hover:bg-gray-50 active:bg-gray-100"
      >
        ☰
      </button>

      {open && (
        <div className="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-50">
          <Link
            href="/chat"
            className="block px-4 py-2 text-sm hover:bg-gray-50"
          >
            Mensajes
          </Link>

          <button
            type="button"
            className="w-full text-left px-4 py-2 text-sm hover:bg-gray-50"
          >
            Notificaciones
          </button>

          <div className="px-4 py-2 text-sm text-gray-500 border-t">
            {userName}
          </div>

          <button
            type="button"
            onClick={() => router.post('/logout')}
            className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50 border-t"
          >
            Log out
          </button>
        </div>
      )}
    </div>
  );
}
