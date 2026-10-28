import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useMemo } from 'react';

type LaravelPaginator<T> = {
  data: T[];
  current_page: number;
  last_page: number;
  prev_page_url:string | null;
  next_page_url: string | null;
  total: number;
};

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
  created_at?: string | null;
};

type MyAdvertisementsProps = {
  auth: any;
  createdAds: LaravelPaginator<Ad>;
}

function OwnerStatusPill({ status }: { status: AdStatus }) {
  const label = status === 'active' ? 'Disponible' : status === 'finished' ? 'Agotado' : 'Desactivado';
  const cls =
    status === 'active'
      ? 'bg-green-50 text-green-700 border-green-200'
      : status === 'finished'
      ? 'bg-red-50 text-red-700 border-red-200'
      : 'bg-gray-50 text-gray-700 border-gray-200';

  return (
    <span className={`inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium ${cls}`}>
      {label}
    </span>
  );
}

export default function MyAdvertisements({ auth, createdAds }: MyAdvertisementsProps) {
  /*
  const myAds: Ad[] = useMemo(() => {
    const list = (createdAds ?? []).filter((ad: any) => ad.author === auth.user.name);
    const getTime = (x: any) => {
      const t = x.created_at ? Date.parse(x.created_at) : NaN;
      return Number.isFinite(t) ? t : x.id;
    };
    return list.sort((a: any, b: any) => getTime(b) - getTime(a));
  }, [createdAds, auth.user.name]);
  */
  //Ahora se recibe un objeto paginador
  const myAds: Ad[] = createdAds.data ?? [];


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
            <>
            <div className="space-y-4">
              {myAds.map((ad) => (
                <div key={ad.id} className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                  <img src={pickImage(ad)} alt={ad.title} className="h-52 w-full object-cover" />

                  <div className="p-4">
                    <div className="flex items-center justify-between gap-2">
                      <h2 className="font-semibold text-gray-900">{ad.title}</h2>
                      <OwnerStatusPill status={(ad.status ?? 'active') as AdStatus} />
                    </div>

                    <p className="mt-1 text-sm text-gray-600">{ad.description}</p>

                    <div className="mt-4 flex gap-2">
                      <Link
                        href={route('advertisement.edit', ad.id)}
                        className="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50"
                      >
                        Editar
                      </Link>

                      <Link
                        href={route('advertisement.show', ad.id)}
                        className="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50"
                      >
                        Ver detalle
                      </Link>

                      <button
                        onClick={() => {
                          if (confirm('¿Eliminar publicación?')) router.delete(route('advertisement.destroy', ad.id));
                        }}
                        className="px-3 py-1.5 rounded-lg border text-sm text-red-600 hover:bg-red-50"
                      >
                        Eliminar
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
            <div className="flex items-center justify-between rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
              <button
                disabled={!createdAds.prev_page_url}
                onClick={() => {
                  if (createdAds.prev_page_url) {
                    router.get(createdAds.prev_page_url);
                  }
                }}
                className="rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
              >
                Anterior
              </button>

              <span className="text-sm text-gray-600">
                Página {createdAds.current_page} de {createdAds.last_page}
              </span>

              <button
                disabled={!createdAds.next_page_url}
                onClick={() => {
                  if (createdAds.next_page_url) {
                    router.get(createdAds.next_page_url);
                  }
                }}
                className="rounded-lg border px-3 py-2 text-sm hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
              >
                Siguiente
              </button>
            </div>
          </>
          )}
          
        </div>

      </div>
    </AuthenticatedLayout>
  );
}
