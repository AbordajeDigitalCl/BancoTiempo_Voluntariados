import { Link } from '@inertiajs/react';

type Usuario = {
  id: number;
  nombre: string;
  apellido: string;
  correo: string;
  telefono?: string;
  ciudad?: string;
  direccion?: string;
  rol?: string;
  imagen?: string | null;
};

export default function FilaUsuario({
  usuario,
  onDelete,
  onEliminar,
}: {
  usuario: Usuario;
  onDelete?: (id: number) => void;
  onEliminar?: (id: number) => void;
}) {
  const fullName = `${usuario.nombre} ${usuario.apellido}`.trim();

  function handleDelete() {
    if (onEliminar) onEliminar(usuario.id);
    else onDelete?.(usuario.id);
  }

  return (
    <div className="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
      <div className="flex items-center gap-3">
        <div className="h-12 w-12 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
          {usuario.imagen ? (
            <img src={usuario.imagen} alt={fullName} className="h-full w-full object-cover" />
          ) : (
            <div className="h-full w-full flex items-center justify-center text-gray-400 text-xs">IMG</div>
          )}
        </div>

        <div className="min-w-0">
          <div className="font-medium text-gray-900 truncate">{fullName}</div>
          <div className="text-sm text-gray-600 truncate">{usuario.correo}</div>

          <div className="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-500">
            {usuario.rol ? (
              <span className="inline-flex items-center rounded-full border border-gray-200 bg-white px-2 py-0.5">
                {usuario.rol}
              </span>
            ) : null}

            {usuario.ciudad ? (
              <span className="inline-flex items-center rounded-full border border-gray-200 bg-white px-2 py-0.5">
                {usuario.ciudad}
              </span>
            ) : null}
          </div>
        </div>
      </div>

      <div className="flex flex-col gap-2 sm:flex-row sm:items-center">
        <Link
          href={`/verperfil-Admin/${usuario.id}`}
          className="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50 text-center"
        >
          Ver perfil
        </Link>

        <Link
          href={`/perfil-Admin/${usuario.id}`}
          className="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-gray-50 text-center"
        >
          Actualizar
        </Link>

        <button
          type="button"
          onClick={handleDelete}
          className="rounded-lg border border-red-200 px-3 py-2 text-sm text-red-600 hover:bg-red-50 text-center"
        >
          Eliminar
        </button>
      </div>
    </div>
  );
}
