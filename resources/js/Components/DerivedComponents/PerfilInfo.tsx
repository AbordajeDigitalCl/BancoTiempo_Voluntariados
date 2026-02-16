import { PerfilData } from '@/mockData/datosMockPerfil';
import {Link} from "@inertiajs/react";
interface PerfilInfoProps {
    perfil: PerfilData & { apellidoPaterno?: string; apellidoMaterno?: string };
}

export default function PerfilInfo({ perfil }: PerfilInfoProps) {
    const getNombreCompleto = () => {
        let nombre = perfil.nombre || '';
        if (perfil.apellidoPaterno) {
            nombre += ' ' + perfil.apellidoPaterno;
        }
        if (perfil.apellidoMaterno) {
            nombre += ' ' + perfil.apellidoMaterno.charAt(0) + '.';
        }
        return nombre;
    };
    return (
        <div className="w-full h-full bg-white">
           
            <div className="hidden md:flex items-center justify-center mt-3">
                <div className="w-full p-4 box-border">
                    <div className="flex w-full h-max">
                        
                        <div className="w-1/3 h-full relative">
                            <img
                                src={perfil.foto}
                                alt="Profile"
                                className="w-full h-full object-cover rounded-lg"
                            />
                            
                            <Link
                                href="/EditarPerfil"
                                className="absolute top-2 right-2 bg-black-200 rounded-full p-2 shadow-lg hover:bg-gray-100 transition flex flex-col items-center"
                                title="Actualizar foto"
                            >
                                <span className="text-lg font-bold">✎</span>
                                <span className="text-xs font-bold text-gray-700">Actualizar</span>
                            </Link>
                        </div>

                   
                        <div className="w-1/3 h-full flex flex-col justify-start pl-4 text-left">
                            <span className="text-2xl font-bold">{getNombreCompleto()}</span>
                            <div className="mt-2 space-y-1">
                                <div><span className="font-bold">Ciudad:</span> <span>{perfil.ciudad}</span></div>
                                <div><span className="font-bold">Teléfono:</span> <span>{perfil.telefono}</span></div>
                                <div><span className="font-bold">Dirección:</span> <span>{perfil.direccion}</span></div>
                                <div><span className="font-bold">Email:</span> <span>{perfil.email}</span></div>
                                <div><span className="font-bold">Fecha de Nacimiento:</span> <span>{perfil.fechaNacimiento}</span></div>
                            </div>
                        </div>

                       
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
                        </div>
                    </div>
                </div>
            </div>

            
            <div className="flex md:hidden flex-col items-center py-6 px-4">
                
                <div className="flex flex-col items-center text-center mb-4">
                    <div className="flex items-center justify-center gap-2">
                        <span className="text-xl font-bold">{perfil.saldoHoras} horas</span>
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
                </div>

             
                <div className="relative mb-4 w-40 h-40">
                    <img
                        src={perfil.foto}
                        alt="Profile"
                        className="w-full h-full object-cover rounded-lg"
                    />
                    
                    <Link
                        href="/EditarPerfil"
                        className="absolute top-2 right-2 bg-gray-200 rounded-full p-2 shadow-lg hover:bg-gray-300 transition flex flex-col items-center text-xs"
                        title="Actualizar foto"
                    >
                        <span className="text-lg font-bold">✎</span>
                    </Link>
                </div>

                <div className="w-full text-left">
                    <span className="text-2xl font-bold block">{getNombreCompleto()}</span>
                    <div className="space-y-2 text-sm">
                        <div><span className="font-bold">Ciudad:</span> <span>{perfil.ciudad}</span></div>
                        <div><span className="font-bold">Teléfono:</span> <span>{perfil.telefono}</span></div>
                        <div><span className="font-bold">Dirección:</span> <span>{perfil.direccion}</span></div>
                        <div><span className="font-bold">Email:</span> <span>{perfil.email}</span></div>
                        <div><span className="font-bold">Fecha de Nacimiento:</span> <span>{perfil.fechaNacimiento}</span></div>
                    </div>
                </div>
            </div>
        </div>
    );
}
