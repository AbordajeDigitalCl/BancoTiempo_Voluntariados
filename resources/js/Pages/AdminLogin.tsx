import { Head, Link, useForm, router } from '@inertiajs/react';

export default function AdminLogin() {
  const { data, setData, post, processing, errors } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  function submit(e: React.FormEvent) {
    e.preventDefault();

    post('/login', {
      preserveScroll: true,
      onSuccess: () => {
        router.visit('/Admin');
      },
    });
  }

  return (
    <>
      <Head title="Admin - Iniciar sesión" />

      <div className="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
        <div className="w-full max-w-4xl bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="grid grid-cols-1 md:grid-cols-2">
            <div className="p-6 sm:p-8">
              <h1 className="text-xl font-semibold text-gray-900">Iniciar sesión</h1>

              <form onSubmit={submit} className="mt-6 space-y-4">
                <div>
                  <label className="block text-xs text-gray-600 mb-1">Correo</label>
                  <input
                    type="email"
                    value={data.email}
                    onChange={(e) => setData('email', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.email && <p className="text-xs text-red-500 mt-1">{errors.email}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Contraseña</label>
                  <input
                    type="password"
                    value={data.password}
                    onChange={(e) => setData('password', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.password && <p className="text-xs text-red-500 mt-1">{errors.password}</p>}
                </div>

                <div className="flex items-center justify-between">
                  <label className="inline-flex items-center gap-2 text-sm text-gray-600">
                    <input
                      type="checkbox"
                      checked={data.remember}
                      onChange={(e) => setData('remember', e.target.checked)}
                      className="rounded border-gray-300"
                    />
                    Recordarme
                  </label>

                  <button type="button" className="text-sm text-gray-600 hover:underline">
                    ¿Olvidó su contraseña?
                  </button>
                </div>

                <button
                  type="submit"
                  disabled={processing}
                  className="w-full rounded-lg bg-black text-white py-2 text-sm font-medium hover:bg-gray-800 disabled:opacity-50"
                >
                  {processing ? 'Ingresando...' : 'Iniciar sesión'}
                </button>

                <div className="text-sm text-gray-600 text-center">
                  ¿No tiene una cuenta?{' '}
                  <Link href="/Admin/Register" className="text-gray-900 hover:underline">
                    Registrarse
                  </Link>
                </div>

                <div className="text-sm text-center">
                  <Link href="/Admin" className="text-gray-600 hover:underline">
                    Volver
                  </Link>
                </div>
              </form>
            </div>

            <div className="hidden md:block bg-[#B56B93]" />
          </div>
        </div>
      </div>
    </>
  );
}
