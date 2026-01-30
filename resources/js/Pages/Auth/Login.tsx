import { useEffect, FormEventHandler } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login({
  status,
  canResetPassword,
}: {
  status?: string;
  canResetPassword: boolean;
}) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: false,
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
  }, []);

  const submit: FormEventHandler = (e) => {
    e.preventDefault();
    post(route('login'));
  };

  return (
    <GuestLayout>
      <Head title="Iniciar sesión" />

      <div className="w-full flex justify-center px-4 py-6">
        <div className="w-full max-w-md">
          {status && (
            <div className="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
              {status}
            </div>
          )}

          <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="p-6">
              <h1 className="text-xl font-semibold text-gray-900">Iniciar sesión</h1>
              <p className="mt-1 text-sm text-gray-600">
                Ingresa con tu correo y contraseña.
              </p>

              <form onSubmit={submit} className="mt-6 space-y-4">
                <div>
                  <InputLabel htmlFor="email" value="Correo electrónico" />

                  <TextInput
                    id="email"
                    type="email"
                    name="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    autoComplete="username"
                    isFocused={true}
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
                    autoComplete="current-password"
                    onChange={(e) => setData('password', e.target.value)}
                    required
                  />

                  <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="flex items-center justify-between">
                  <label className="flex items-center">
                    <Checkbox
                      name="remember"
                      checked={data.remember}
                      onChange={(e) => setData('remember', e.target.checked)}
                    />
                    <span className="ms-2 text-sm text-gray-600">Recordarme</span>
                  </label>

                  {canResetPassword && (
                    <Link
                      href={route('password.request')}
                      className="text-sm text-gray-600 hover:text-gray-900 hover:underline"
                    >
                      ¿Olvidaste tu contraseña?
                    </Link>
                  )}
                </div>

                <PrimaryButton
                  className="w-full justify-center"
                  disabled={processing}
                >
                  Iniciar sesión
                </PrimaryButton>

                <div className="flex items-center justify-center gap-2 pt-2 text-sm text-gray-600">
                  <span>¿No tienes una cuenta?</span>
                  <Link
                    href={route('register')}
                    className="font-medium text-gray-900 hover:underline"
                  >
                    Registrarse
                  </Link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </GuestLayout>
  );
}
