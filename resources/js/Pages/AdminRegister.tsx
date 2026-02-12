import { Head, Link, useForm, router } from '@inertiajs/react';

export default function AdminRegister() {
  const { data, setData, post, processing, errors } = useForm({
    name: '',
    Apellido_Paterno: '',
    Apellido_Materno: '',
    Age: '',
    Telefono: '',
    Direccion: '',
    Ciudad: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  function submit(e: React.FormEvent) {
    e.preventDefault();

    post('/register', {
      preserveScroll: true,
      onSuccess: () => {
        router.visit('/Admin');
      },
    });
  }

  return (
    <>
      <Head title="Admin - Registrarse" />

      <div className="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">
        <div className="w-full max-w-4xl bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="grid grid-cols-1 md:grid-cols-2">
            <div className="p-6 sm:p-8">
              <h1 className="text-xl font-semibold text-gray-900">Registrarse</h1>

              <form onSubmit={submit} className="mt-6 space-y-4">
                <div>
                  <label className="block text-xs text-gray-600 mb-1">Nombre</label>
                  <input
                    type="text"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Apellido Paterno</label>
                  <input
                    type="text"
                    value={data.Apellido_Paterno}
                    onChange={(e) => setData('Apellido_Paterno', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.Apellido_Paterno && <p className="text-xs text-red-500 mt-1">{errors.Apellido_Paterno}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Apellido Materno</label>
                  <input
                    type="text"
                    value={data.Apellido_Materno}
                    onChange={(e) => setData('Apellido_Materno', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.Apellido_Materno && <p className="text-xs text-red-500 mt-1">{errors.Apellido_Materno}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Edad</label>
                  <input
                    type="text"
                    value={data.Age}
                    onChange={(e) => setData('Age', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.Age && <p className="text-xs text-red-500 mt-1">{errors.Age}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Teléfono</label>
                  <input
                    type="text"
                    value={data.Telefono}
                    onChange={(e) => setData('Telefono', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.Telefono && <p className="text-xs text-red-500 mt-1">{errors.Telefono}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Dirección</label>
                  <input
                    type="text"
                    value={data.Direccion}
                    onChange={(e) => setData('Direccion', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.Direccion && <p className="text-xs text-red-500 mt-1">{errors.Direccion}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Ciudad</label>
                  <input
                    type="text"
                    value={data.Ciudad}
                    onChange={(e) => setData('Ciudad', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.Ciudad && <p className="text-xs text-red-500 mt-1">{errors.Ciudad}</p>}
                </div>

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Correo electrónico</label>
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

                <div>
                  <label className="block text-xs text-gray-600 mb-1">Confirmar contraseña</label>
                  <input
                    type="password"
                    value={data.password_confirmation}
                    onChange={(e) => setData('password_confirmation', e.target.value)}
                    className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  />
                  {errors.password_confirmation && <p className="text-xs text-red-500 mt-1">{errors.password_confirmation}</p>}
                </div>

                <button
                  type="submit"
                  disabled={processing}
                  className="w-full rounded-lg bg-black text-white py-2 text-sm font-medium hover:bg-gray-800 disabled:opacity-50"
                >
                  {processing ? 'Registrando...' : 'Registrarse'}
                </button>

                <div className="text-sm text-gray-600 text-center">
                  ¿Ya tiene una cuenta?{' '}
                  <Link href="/Admin/Login" className="text-gray-900 hover:underline">
                    Iniciar sesión
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
