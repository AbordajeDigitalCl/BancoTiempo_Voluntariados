import { useEffect, FormEventHandler } from 'react';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        Apellido_Materno: '',
        Apellido_Paterno: '',
        Age: '',
        Telefono: '',
        Direccion: '',
        Ciudad: '',
        email: '',
        password: '',
        password_confirmation: '',
        avatar: null as File | null,
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('register'), {
            forceFormData: true,
        });
    };

    return (
        <GuestLayout>
            <Head title="Register" />

            <form onSubmit={submit} className="w-full flex justify-center px-4 py-8">
                <div className="w-full max-w-md">
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div className="p-5">
                            <h1 className="text-xl font-semibold text-gray-900">Registrarse</h1>

                            <div className="mt-4 space-y-4">
                                <div>
                                    <div className="border border-dashed border-gray-300 rounded-xl p-4 flex flex-col items-center justify-center gap-2">
                                        <div className="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg
                                                className="w-7 h-7 text-gray-500"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                            >
                                                <path d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z" />
                                            </svg>
                                        </div>

                                        <div className="text-sm text-gray-700">Foto de perfil</div>

                                        <label className="inline-flex items-center gap-2 cursor-pointer">
                                            <span className="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm hover:bg-gray-50">
                                                Seleccionar archivo
                                            </span>

                                            <input
                                                type="file"
                                                className="hidden"
                                                accept="image/*"
                                                onChange={(e) =>
                                                    setData('avatar', e.target.files ? e.target.files[0] : null)
                                                }
                                            />
                                        </label>

                                        <InputError message={(errors as any).avatar} className="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel htmlFor="name" value="Nombre" />

                                    <TextInput
                                        id="name"
                                        name="name"
                                        value={data.name}
                                        className="mt-1 block w-full"
                                        autoComplete="name"
                                        isFocused={true}
                                        onChange={(e) => setData('name', e.target.value)}
                                        required
                                    />

                                    <InputError message={errors.name} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="Apellido_Paterno" value="Apellido Paterno" />

                                    <TextInput
                                        id="Apellido_Paterno"
                                        type="text"
                                        name="Apellido_Paterno"
                                        value={data.Apellido_Paterno}
                                        className="mt-1 block w-full"
                                        autoComplete="family-name"
                                        onChange={(e) => setData('Apellido_Paterno', e.target.value)}
                                        required
                                    />

                                    <InputError message={(errors as any).Apellido_Paterno} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="Apellido_Materno" value="Apellido Materno" />

                                    <TextInput
                                        id="Apellido_Materno"
                                        type="text"
                                        name="Apellido_Materno"
                                        value={data.Apellido_Materno}
                                        className="mt-1 block w-full"
                                        autoComplete="additional-name"
                                        onChange={(e) => setData('Apellido_Materno', e.target.value)}
                                        required
                                    />

                                    <InputError message={(errors as any).Apellido_Materno} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="Age" value="Fecha de Nacimiento" />

                                    <TextInput
                                        id="Age"
                                        type="date"
                                        name="Age"
                                        value={data.Age}
                                        className="mt-1 block w-full"
                                        autoComplete="bday"
                                        onChange={(e) => setData('Age', e.target.value)}
                                        required
                                    />

                                    <InputError message={(errors as any).Age} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="Telefono" value="Telefono" />

                                    <TextInput
                                        id="Telefono"
                                        type="tel"
                                        name="Telefono"
                                        value={data.Telefono}
                                        className="mt-1 block w-full"
                                        autoComplete="tel"
                                        onChange={(e) => setData('Telefono', e.target.value)}
                                        required
                                    />

                                    <InputError message={(errors as any).Telefono} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="Direccion" value="Direccion" />

                                    <TextInput
                                        id="Direccion"
                                        type="text"
                                        name="Direccion"
                                        value={data.Direccion}
                                        className="mt-1 block w-full"
                                        autoComplete="street-address"
                                        onChange={(e) => setData('Direccion', e.target.value)}
                                        required
                                    />

                                    <InputError message={(errors as any).Direccion} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="Ciudad" value="Ciudad" />

                                    <TextInput
                                        id="Ciudad"
                                        type="text"
                                        name="Ciudad"
                                        value={data.Ciudad}
                                        className="mt-1 block w-full"
                                        autoComplete="address-level2"
                                        onChange={(e) => setData('Ciudad', e.target.value)}
                                        required
                                    />

                                    <InputError message={(errors as any).Ciudad} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="email" value="Correo electronico" />

                                    <TextInput
                                        id="email"
                                        type="email"
                                        name="email"
                                        value={data.email}
                                        className="mt-1 block w-full"
                                        autoComplete="username"
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                    />

                                    <InputError message={errors.email} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="password" value="Contraseña" />

                                    <TextInput
                                        id="password"
                                        type="password"
                                        name="password"
                                        value={data.password}
                                        className="mt-1 block w-full"
                                        autoComplete="new-password"
                                        onChange={(e) => setData('password', e.target.value)}
                                        required
                                    />

                                    <InputError message={errors.password} className="mt-2" />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="password_confirmation" value="Confirmar contraseña" />

                                    <TextInput
                                        id="password_confirmation"
                                        type="password"
                                        name="password_confirmation"
                                        value={data.password_confirmation}
                                        className="mt-1 block w-full"
                                        autoComplete="new-password"
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        required
                                    />

                                    <InputError message={errors.password_confirmation} className="mt-2" />
                                </div>

                                <div className="flex items-center justify-end mt-4">
                                    <Link
                                        href={route('login')}
                                        className="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        ¿Ya tienes una cuenta?
                                    </Link>

                                    <PrimaryButton className="ms-4" disabled={processing}>
                                        Iniciar sesión
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </GuestLayout>
    );
}
