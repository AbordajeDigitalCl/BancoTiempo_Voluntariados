import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useMemo } from 'react';

type AdStatus = 'active' | 'paused' | 'finished';

type Ad = {
  id: number;
  title: string;
  description: string;
  hours: number;
  type: 'Servicio' | 'Producto';
  mode: 'Oferta' | 'Demanda';
  status?: AdStatus;
  author?: string;
  imageUrl?: string;
  imageUrls?: string[];
};

function StatusPill({ status }: { status: AdStatus }) {
  const label = status === 'active' ? 'Activa' : status === 'paused' ? 'Pausada' : 'Finalizada';
  const cls =
    status === 'active'
      ? 'bg-green-50 text-green-700 border-green-200'
      : status === 'paused'
      ? 'bg-yellow-50 text-yellow-800 border-yellow-200'
      : 'bg-gray-50 text-gray-700 border-gray-200';

  return (
    <span className={`inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium ${cls}`}>
      {label}
    </span>
  );
}

export default function MyAdvertisements({ auth, createdAds }: any) {
  const myAds: Ad[] = useMemo(() => {
    return (createdAds ?? []).filter((ad: any) => ad.author === auth.user.name);
  }, [createdAds, auth.user.name]);

  const pickImage = (ad: Ad) => {
    if (ad.imageUrls && ad.imageUrls.length > 0) return ad.imageUrls[0];
    return ad.imageUrl || 'https://picsum.photos/seed/demo/900/600';
  };

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Mis publicaciones" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-3xl space-y-4">
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-start justify-between">
            <div>
              <h1 className="text-lg font-semibold">Mis publicaciones</h1>
              <p className="mt-1 text-sm text-gray-600">Aquí ves solo las publicaciones que tú creaste.</p>
            </div>

            <Link
              href={route('advertisement.create')}
              className="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-gray-200 hover:bg-gray-50"
              title="Crear publicación"
            >
              <span className="text-xl leading-none">+</span>
            </Link>
          </div>

          {myAds.length === 0 ? (
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-4 text-sm text-gray-600">
              No has creado publicaciones todavía.
            </div>
          ) : (
            <div className="space-y-4">
              {myAds.map((ad) => (
                <div key={ad.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                  <img src={pickImage(ad)} alt={ad.title} className="h-52 w-full object-cover" />

                  <div className="p-4">
                    <div className="flex items-center justify-between gap-2">
                      <h2 className="font-semibold text-gray-900">{ad.title}</h2>
                      <StatusPill status={(ad.status ?? 'active') as AdStatus} />
                    </div>

                    <p className="mt-1 text-sm text-gray-600">{ad.description}</p>

                    <div className="mt-4 flex gap-2">
                      <Link
                        href={route('advertisement.edit', ad.id)}
                        className="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50"
                      >
                        Editar
                      </Link>

                      <button
                        onClick={() => router.delete(route('advertisement.destroy', ad.id))}
                        className="px-3 py-1.5 rounded-lg border text-sm text-red-600 hover:bg-red-50"
                      >
                        Eliminar
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
