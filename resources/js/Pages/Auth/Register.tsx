import { useEffect, useMemo, useState, FormEventHandler } from 'react';
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

    const [photoPreview, setPhotoPreview] = useState<string | null>(null);

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
            if (photoPreview) URL.revokeObjectURL(photoPreview);
        };
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const hasAnyError = useMemo(() => Object.keys(errors || {}).length > 0, [errors]);

    const onPickPhoto = (file: File | null) => {
        setData('profile_photo', file);

        if (photoPreview) URL.revokeObjectURL(photoPreview);
        if (!file) {
            setPhotoPreview(null);
            return;
        }
        const url = URL.createObjectURL(file);
        setPhotoPreview(url);
    };

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('register'), {
            forceFormData: true, // <-- clave para que SIEMPRE mande multipart/form-data (foto)
            preserveScroll: true,
        });
    };

    const inputClass = (key: string) =>
        'mt-1 block w-full ' + (errors && (errors as any)[key] ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : '');

    return (
        <GuestLayout>
            <Head title="Registro" />

            <form onSubmit={submit} className="space-y-4">
                {/* Banner claro si hay errores */}
                {hasAnyError && (
                    <div className="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        Revisa los campos marcados. Hay datos inválidos o faltantes.
                    </div>
                )}

                {/* Foto con preview real */}
                <div className="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4">
                    <div className="flex flex-col items-center text-center gap-3">
                        <div className="w-24 h-24 rounded-full bg-white border border-gray-200 overflow-hidden flex items-center justify-center">
                            {photoPreview ? (
                                <img
                                    src={photoPreview}
                                    alt="Previsualización foto de perfil"
                                    className="w-full h-full object-cover"
                                />
                            ) : (
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor" className="text-gray-500">
                                    <path d="M12 12c2.761 0 5-2.239 5-5S14.761 2 12 2 7 4.239 7 7s2.239 5 5 5zm0 2c-3.866 0-7 2.239-7 5v1h14v-1c0-2.761-3.134-5-7-5z" />
                                </svg>
                            )}
                        </div>

                        <div className="text-sm text-gray-700 font-medium">Foto de perfil</div>

                        <input
                            type="file"
                            accept="image/*"
                            className={
                                'block w-full text-sm file:mr-3 file:rounded-lg file:border file:border-gray-300 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-50 ' +
                                (errors && (errors as any).profile_photo ? 'text-red-700' : '')
                            }
                            onChange={(e) => onPickPhoto(e.target.files ? e.target.files[0] : null)}
                        />
                        <InputError message={(errors as any).profile_photo} className="mt-1" />
                    </div>
                </div>

                <div>
                    <InputLabel htmlFor="name" value="Nombre" />
                    <TextInput
                        id="name"
                        name="name"
                        value={data.name}
                        className={inputClass('name')}
                        autoComplete="name"
                        isFocused={true}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).name} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="Apellido_Paterno" value="Apellido Paterno" />
                    <TextInput
                        id="Apellido_Paterno"
                        type="text"
                        name="Apellido_Paterno"
                        value={data.Apellido_Paterno}
                        className={inputClass('Apellido_Paterno')}
                        autoComplete="family-name"
                        onChange={(e) => setData('Apellido_Paterno', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).Apellido_Paterno} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="Apellido_Materno" value="Apellido Materno" />
                    <TextInput
                        id="Apellido_Materno"
                        type="text"
                        name="Apellido_Materno"
                        value={data.Apellido_Materno}
                        className={inputClass('Apellido_Materno')}
                        autoComplete="additional-name"
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
                        className={inputClass('Age')}
                        onChange={(e) => setData('Age', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).Age} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="Telefono" value="Teléfono" />
                    <TextInput
                        id="Telefono"
                        type="tel"
                        name="Telefono"
                        value={data.Telefono}
                        className={inputClass('Telefono')}
                        autoComplete="tel"
                        onChange={(e) => setData('Telefono', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).Telefono} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="Direccion" value="Dirección" />
                    <TextInput
                        id="Direccion"
                        type="text"
                        name="Direccion"
                        value={data.Direccion}
                        className={inputClass('Direccion')}
                        autoComplete="street-address"
                        onChange={(e) => setData('Direccion', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).Direccion} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="Ciudad" value="Ciudad" />
                    <TextInput
                        id="Ciudad"
                        type="text"
                        name="Ciudad"
                        value={data.Ciudad}
                        className={inputClass('Ciudad')}
                        autoComplete="address-level2"
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
                        className={inputClass('email')}
                        autoComplete="username"
                        onChange={(e) => setData('email', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).email} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="password" value="Contraseña" />
                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className={inputClass('password')}
                        autoComplete="new-password"
                        onChange={(e) => setData('password', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).password} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="password_confirmation" value="Confirmar contraseña" />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className={inputClass('password_confirmation')}
                        autoComplete="new-password"
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        required
                    />
                    <InputError message={(errors as any).password_confirmation} className="mt-2" />
                </div>

                <div className="flex items-center justify-between pt-2">
                    <div className="text-sm text-gray-600">
                        ¿Ya tienes una cuenta?{' '}
                        <Link href={route('login')} className="underline hover:text-gray-900">
                            Iniciar sesión
                        </Link>
                    </div>

                    <PrimaryButton disabled={processing} className="ms-4">
                        Crear cuenta
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}
