import { PerfilEditable } from '@/mockData/datosMockEditarPerfil';
import { useFormState } from '@/hooks/usarEstadoFormulario';
import PasswordInput from './EntradaContrasena';
import { useState } from 'react';

interface FormEditarPerfilProps {
    datosIniciales: PerfilEditable;
    fotoInicial?: string;
}

export default function FormEditarPerfil({ datosIniciales, fotoInicial = "https://picsum.photos/150?random=1" }: FormEditarPerfilProps) {
    const {
        formData,
        mostrarContrasena,
        mostrarConfirmar,
        handleChange,
        handleTogglePassword,
        handleToggleConfirm,
        handleSubmit
    } = useFormState(datosIniciales);

    const [foto, setFoto] = useState(fotoInicial);
    const [previewFoto, setPreviewFoto] = useState(fotoInicial);

    const handleFotoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            
            const reader = new FileReader();
            reader.onloadend = () => {
                setPreviewFoto(reader.result as string);
                setFoto(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    const onSubmit = handleSubmit((data) => {
        console.log('Datos a guardar:', data);
        console.log('Foto:', foto);
    });

    return (
        <form onSubmit={onSubmit} className="w-full max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
            <h1 className="text-3xl font-bold mb-8 text-gray-800">Editar Información de Perfil</h1>

            {/* Sección de Foto */}
            <div className="mb-8 pb-8 border-b border-gray-200">
                <div className="flex items-center gap-6">
                    {/* Foto actual */}
                    <img
                        src={previewFoto}
                        alt="Foto de perfil"
                        className="w-32 h-32 rounded-full object-cover border-4 border-gray-300 flex-shrink-0"
                    />
                    
                    {/* Botón de cambiar foto */}
                    <label className="cursor-pointer flex-shrink-0">
                        <div className="w-40 h-40 bg-gray-300 rounded-lg border-4 border-gray-400 flex flex-col items-center justify-end p-4 hover:bg-gray-400 transition">
                            <input
                                type="file"
                                accept="image/*"
                                onChange={handleFotoChange}
                                className="hidden"
                            />
                            <button
                                type="button"
                                onClick={(e) => {
                                    e.preventDefault();
                                    const input = document.querySelector('input[type="file"]') as HTMLInputElement;
                                    input?.click();
                                }}
                                className="bg-white text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition"
                            >
                                Actualizar foto
                            </button>
                        </div>
                    </label>
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {/* Nombre */}
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Nombre
                    </label>
                    <input
                        type="text"
                        name="nombre"
                        value={formData.nombre}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ingresa tu nombre"
                    />
                </div>

                {/* Apellido Paterno */}
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Apellido Paterno
                    </label>
                    <input
                        type="text"
                        name="apellidoPaterno"
                        value={formData.apellidoPaterno}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ingresa tu apellido paterno"
                    />
                </div>

                {/* Apellido Materno */}
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Apellido Materno
                    </label>
                    <input
                        type="text"
                        name="apellidoMaterno"
                        value={formData.apellidoMaterno}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ingresa tu apellido materno"
                    />
                </div>

                {/* Email */}
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Correo Electrónico
                    </label>
                    <input
                        type="email"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="tu.email@ejemplo.com"
                    />
                </div>

                {/* Contraseña */}
                <PasswordInput
                    name="contrasena"
                    value={formData.contrasena}
                    onChange={handleChange}
                    mostrarPassword={mostrarContrasena}
                    onToggle={handleTogglePassword}
                    label="Contraseña"
                    placeholder="Ingresa tu contraseña"
                />

                {/* Confirmar Contraseña */}
                <PasswordInput
                    name="confirmarContrasena"
                    value={formData.confirmarContrasena}
                    onChange={handleChange}
                    mostrarPassword={mostrarConfirmar}
                    onToggle={handleToggleConfirm}
                    label="Confirmar Contraseña"
                    placeholder="Confirma tu contraseña"
                />

                {/* Fecha de Nacimiento con calendario */}
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Fecha de Nacimiento
                    </label>
                    <input
                        type="date"
                        name="fechaNacimiento"
                        value={formData.fechaNacimiento}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                {/* Teléfono */}
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Teléfono
                    </label>
                    <input
                        type="tel"
                        name="telefono"
                        value={formData.telefono}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="+56 9 1234 5678"
                    />
                </div>

                {/* Dirección */}
                <div className="md:col-span-2">
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Dirección
                    </label>
                    <input
                        type="text"
                        name="direccion"
                        value={formData.direccion}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Calle, número, piso (si aplica)"
                    />
                </div>

                {/* Ciudad */}
                <div className="md:col-span-2">
                    <label className="block text-sm font-bold text-gray-700 mb-2">
                        Ciudad
                    </label>
                    <input
                        type="text"
                        name="ciudad"
                        value={formData.ciudad}
                        onChange={handleChange}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tu ciudad"
                    />
                </div>
            </div>

            {/* Botones de acción */}
            <div className="flex justify-between gap-4 mt-8">
                <button
                    type="button"
                    onClick={() => window.history.back()}
                    className="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-bold"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    className="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-bold"
                >
                    Guardar Cambios
                </button>
            </div>
        </form>
    );
}
