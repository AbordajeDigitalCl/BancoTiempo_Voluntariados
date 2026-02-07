import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

type Noti = {
  id: string | number;
  title: string;
  body: string;
  time: string;
  unread?: boolean;
};

export default function Notifications({ auth }: any) {
  const page: any = usePage();

  const items: Noti[] = (page?.props?.messageNotifications ?? []) as Noti[];
  const unreadCount: number = Number(page?.props?.unreadMessageCount ?? 0);

  useEffect(() => {
    if (unreadCount > 0) {
      router.post('/Notificaciones/read-all', {}, { preserveScroll: true });
    }
  }, []);

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Notificaciones" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-2xl space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h1 className="text-lg font-semibold">Notificaciones</h1>
                <p className="mt-1 text-sm text-gray-600">
                  {unreadCount > 0 ? `Tienes ${unreadCount} sin leer.` : 'No tienes mensajes sin leer.'}
                </p>
              </div>

              <Link
                href={route('chat')}
                className="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm hover:bg-gray-50"
              >
                Ir a Mensajes
              </Link>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {items.length === 0 ? (
              <div className="px-4 py-8 text-sm text-gray-600">
                No hay notificaciones de mensajes todavía.
              </div>
            ) : (
              items.map((n) => (
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
              ))
            )}

            <div className="px-4 py-3 border-t border-gray-100">
              <Link href={route('chat')} className="text-sm text-gray-700 hover:underline">
                Ver conversación
              </Link>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
