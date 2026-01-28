import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

type Noti = {
  id: number;
  title: string;
  body: string;
  time: string;
  unread?: boolean;
};

export default function Notifications({ auth }: any) {
  const items: Noti[] = [
    { id: 1, title: 'Nueva publicación', body: 'Marcelo publicó “Gasfiter”.', time: 'Hace 2 min', unread: true },
    { id: 2, title: 'Mensaje', body: 'Pedro Juarez te envió un mensaje.', time: 'Hace 1 hora', unread: true },
    { id: 3, title: 'Actualización', body: 'Tu publicación fue guardada.', time: 'Ayer', unread: false },
  ];

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Notificaciones" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-2xl space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <h1 className="text-lg font-semibold">Notificaciones</h1>
            <p className="mt-1 text-sm text-gray-600">Actividad reciente de tu cuenta.</p>
          </div>

          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {items.map((n) => (
              <div
                key={n.id}
                className="px-4 py-3 border-b border-gray-100 last:border-b-0 flex gap-3"
              >
                <div className="pt-1">
                  <span
                    className={
                      'inline-block w-2 h-2 rounded-full ' +
                      (n.unread ? 'bg-blue-600' : 'bg-gray-300')
                    }
                  />
                </div>

                <div className="flex-1">
                  <div className="flex items-start justify-between gap-3">
                    <div className="font-medium text-sm text-gray-900">{n.title}</div>
                    <div className="text-xs text-gray-500 whitespace-nowrap">{n.time}</div>
                  </div>
                  <div className="mt-1 text-sm text-gray-600">{n.body}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
