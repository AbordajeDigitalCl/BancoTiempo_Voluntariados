import { useEffect, FormEventHandler } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm, router } from '@inertiajs/react';

export default function AdminRegister() {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    Apellido_Paterno: '',
    Apellido_Materno: '',
    Age: '', // <-- backend: date
    Telefono: '',
    Direccion: '',
    Ciudad: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  useEffect(() => {
    return () => reset('password', 'password_confirmation');
  }, []);

  const submit: FormEventHandler = (e) => {
    e.preventDefault();

    post(route('register'), {
      preserveScroll: true,
      onSuccess: () => {
        // ✅ luego de registrarse, llevar al AdminHome
        router.visit('/Admin');
      },
    });
  };

  return (
    <GuestLayout>
      <Head title="Registro Admin" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
          <div className="p-6">
            <h1 className="text-xl font-semibold text-gray-900">Registro Administrador</h1>
            <p className="mt-1 text-sm text-gray-600">Crea una cuenta para el panel de administración.</p>

            <form onSubmit={submit} className="mt-6 space-y-4">
              <div>
                <InputLabel htmlFor="name" value="Nombre" />
                <TextInput
                  id="name"
                  name="name"
                  value={data.name}
                  className="mt-1 block w-full"
                  isFocused
                  onChange={(e) => setData('name', e.target.value)}
                  required
                />
                <InputError message={errors.name} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="Apellido_Paterno" value="Apellido Paterno" />
                <TextInput
                  id="Apellido_Paterno"
                  name="Apellido_Paterno"
                  value={data.Apellido_Paterno}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('Apellido_Paterno', e.target.value)}
                  required
                />
                <InputError message={(errors as any).Apellido_Paterno} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="Apellido_Materno" value="Apellido Materno" />
                <TextInput
                  id="Apellido_Materno"
                  name="Apellido_Materno"
                  value={data.Apellido_Materno}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('Apellido_Materno', e.target.value)}
                  required
                />
                <InputError message={(errors as any).Apellido_Materno} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="Age" value="Fecha de nacimiento" />
                <TextInput
                  id="Age"
                  type="date"
                  name="Age"
                  value={data.Age}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('Age', e.target.value)}
                  required
                />
                <InputError message={(errors as any).Age} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="Telefono" value="Teléfono" />
                <TextInput
                  id="Telefono"
                  name="Telefono"
                  value={data.Telefono}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('Telefono', e.target.value)}
                  required
                />
                <InputError message={(errors as any).Telefono} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="Direccion" value="Dirección" />
                <TextInput
                  id="Direccion"
                  name="Direccion"
                  value={data.Direccion}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('Direccion', e.target.value)}
                  required
                />
                <InputError message={(errors as any).Direccion} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="Ciudad" value="Ciudad" />
                <TextInput
                  id="Ciudad"
                  name="Ciudad"
                  value={data.Ciudad}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('Ciudad', e.target.value)}
                  required
                />
                <InputError message={(errors as any).Ciudad} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="email" value="Correo electrónico" />
                <TextInput
                  id="email"
                  type="email"
                  name="email"
                  value={data.email}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('email', e.target.value)}
                  required
                />
                <InputError message={errors.email} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="password" value="Contraseña" />
                <TextInput
                  id="password"
                  type="password"
                  name="password"
                  value={data.password}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('password', e.target.value)}
                  required
                />
                <InputError message={errors.password} className="mt-2" />
              </div>

              <div>
                <InputLabel htmlFor="password_confirmation" value="Confirmar contraseña" />
                <TextInput
                  id="password_confirmation"
                  type="password"
                  name="password_confirmation"
                  value={data.password_confirmation}
                  className="mt-1 block w-full"
                  onChange={(e) => setData('password_confirmation', e.target.value)}
                  required
                />
                <InputError message={(errors as any).password_confirmation} className="mt-2" />
              </div>

              <PrimaryButton className="w-full justify-center" disabled={processing}>
                Registrarse
              </PrimaryButton>

              <div className="flex items-center justify-center gap-2 pt-2 text-sm text-gray-600">
                <span>¿Ya tienes cuenta?</span>
                <Link href="/Admin/Login" className="font-medium text-gray-900 hover:underline">
                  Iniciar sesión (Admin)
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </GuestLayout>
  );
}
