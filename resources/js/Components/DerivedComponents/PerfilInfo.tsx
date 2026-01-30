import { PerfilData } from '@/mockData/datosMockPerfil';

interface PerfilInfoProps {
    perfil: PerfilData;
}

export default function PerfilInfo({ perfil }: PerfilInfoProps) {
    return (
            <div className="w-full h-full flex items-center justify-center mt-3 bg-white">    
                <div className="w-full p-4 box-border">
                    <div className="flex w-full h-max">
                        {/* Left image (1/3 width) */}
                        <div className="w-1/3 h-full">
                            <img
                                src={perfil.foto}
                                alt="Profile"
                                className="w-full h-full object-cover rounded-lg"
                            />
                        </div>

                        {/* Middle list (1/3 width) */}
                        <div className="w-1/3 h-full flex flex-col justify-start pl-4 text-left">
                            <span className="text-2xl font-bold">{perfil.nombre}</span>
                            <span className="text-xs text-left">{perfil.titulo}</span>
                            <div className="mt-2 space-y-1">
                                <div><span className="font-bold">Ciudad:</span> <span>{perfil.ciudad}</span></div>
                                <div><span className="font-bold">Teléfono:</span> <span>{perfil.telefono}</span></div>
                                <div><span className="font-bold">Dirección:</span> <span>{perfil.direccion}</span></div>
                                <div><span className="font-bold">Email:</span> <span>{perfil.email}</span></div>
                                <div><span className="font-bold">Fecha de Nacimiento:</span> <span>{perfil.fechaNacimiento}</span></div>
                            </div>
                        </div>

                        {/* Right section (1/3 width) aligned right */}
                        <div className="w-1/3 h-full flex flex-col justify-start items-end pr-4">
                            <div className="flex items-center text-right">
                                <span className="text-xl font-bold mr-2">{perfil.saldoHoras} horas</span>
                                <div className="flex flex-col items-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="w-6 h-6"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        strokeWidth="2"
                                    >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                    <span className="text-sm">Saldo</span>
                                </div>
                            </div>
                        <span className="text-gray-500 text-xs mt-2 text-right">
                            Expiran el {perfil.fechaExpiracion}
                        </span>
                        </div>
                    </div>
                </div>
            </div>
    );
}
