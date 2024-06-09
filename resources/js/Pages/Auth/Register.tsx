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

            <form onSubmit={submit}>
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
                        autoComplete="apellido-paterno"
                        onChange={(e) => setData('Apellido_Paterno', e.target.value)}
                        required
                    />

                    <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="Apellido_Materno" value="Apellido Materno" />

                    <TextInput
                        id="Apellido_Materno"
                        type="text"
                        name="Apellido_Materno"
                        value={data.Apellido_Materno}
                        className="mt-1 block w-full"
                        autoComplete="apellido-materno"
                        onChange={(e) => setData('Apellido_Materno', e.target.value)}
                        required
                    />

                    <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="Age" value="Fecha de Nacimiento" />

                    <TextInput
                        id="Age"
                        type="Date"
                        name="Age"
                        value={data.Age}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData('Age', e.target.value)}
                        required
                    />

                    <InputError message={errors.Age} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="Telefono" value="Telefono" />

                    <TextInput
                        id="Telefono"
                        type="tel"
                        name="Telefono"
                        value={data.Telefono}
                        className="mt-1 block w-full"
                        autoComplete="Telefono"
                        onChange={(e) => setData('Telefono', e.target.value)}
                        required
                    />

                    <InputError message={errors.password_confirmation} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="Direccion" value="Direccion" />

                    <TextInput
                        id="Direccion"
                        type="text"
                        name="Direccion"
                        value={data.Direccion}
                        className="mt-1 block w-full"
                        autoComplete="direccion"
                        onChange={(e) => setData('Direccion', e.target.value)}
                        required
                    />

                    <InputError message={errors.Direccion} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="Ciudad" value="Ciudad" />

                    <TextInput
                        id="Ciudad"
                        type="text"
                        name="Ciudad"
                        value={data.Ciudad}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData('Ciudad', e.target.value)}
                        required
                    />

                    <InputError message={errors.Ciudad} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="email" value="Email" />

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
                    <InputLabel htmlFor="password" value="Password" />

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
                    <InputLabel htmlFor="password_confirmation" value="Confirm Password" />

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
                        Already registered?
                    </Link>

                    <PrimaryButton className="ms-4" disabled={processing}>
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
