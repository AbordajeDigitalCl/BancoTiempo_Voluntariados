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
        profile_photo: null as File | null,
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register'));
    };

    return (
        <GuestLayout>
            <Head title="Register" />

            <form onSubmit={submit} className="space-y-4">
                <div className="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4">
                    <div className="flex flex-col items-center text-center gap-3">
                        <div className="w-16 h-16 rounded-full bg-white border border-gray-200 flex items-center justify-center">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" className="text-gray-500">
                                <path d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5zm0 2c-3.866 0-7 2.239-7 5v1h14v-1c0-2.761-3.134-5-7-5z" />
                            </svg>
                        </div>

                        <div className="text-sm text-gray-700 font-medium">Foto de perfil</div>

                        <input
                            type="file"
                            accept="image/*"
                            className="block w-full text-sm file:mr-3 file:rounded-lg file:border file:border-gray-300 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-50"
                            onChange={(e) =>
                                setData('profile_photo', e.target.files ? e.target.files[0] : null)
                            }
                        />
                        <InputError message={errors.profile_photo as any} className="mt-1" />
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

                <div>
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
                    <InputError message={errors.Apellido_Paterno as any} className="mt-2" />
                </div>

                <div>
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
                    <InputError message={errors.Apellido_Materno as any} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="Age" value="Fecha de Nacimiento" />
                    <TextInput
                        id="Age"
                        type="date"
                        name="Age"
                        value={data.Age}
                        className="mt-1 block w-full"
                        onChange={(e) => setData('Age', e.target.value)}
                        required
                    />
                    <InputError message={errors.Age} className="mt-2" />
                </div>

                <div>
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
                    <InputError message={errors.Telefono as any} className="mt-2" />
                </div>

                <div>
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
                    <InputError message={errors.Direccion} className="mt-2" />
                </div>

                <div>
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
                    <InputError message={errors.Ciudad} className="mt-2" />
                </div>

                <div>
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

                <div>
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

                <div>
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

                <div className="flex items-center justify-between pt-2">
                    <div className="text-sm text-gray-600">
                        ¿Ya tienes una cuenta?{' '}
                        <Link
                            href={route('login')}
                            className="underline hover:text-gray-900"
                        >
                            Iniciar sesión
                        </Link>
                    </div>

                    <PrimaryButton disabled={processing} className="ms-4">
                        Registrarse
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
