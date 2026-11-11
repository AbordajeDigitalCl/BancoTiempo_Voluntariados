import { Mensaje } from '@/mockData/datosMockChatMensajes';

interface GloboMensajeProps {
    mensaje: Mensaje;
}

/**
 * Extraer solo HH:mm de un formato HH:mm:ss
 * Si ya es HH:mm, devuelve igual
 */
const extraerHora = (hora: string): string => {
    const partes = hora.split(':');
    return partes.length > 2 ? `${partes[0]}:${partes[1]}` : hora;
};

const formatearFechaHoraLocal = (fecha: string, hora: string): string => {
    const [dia, mes, anio] = fecha.split('/');

    const fechaUTC = new Date(
        `${anio}-${mes}-${dia}T${hora}Z`
    );

    return fechaUTC.toLocaleString(undefined, {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
};

export default function GloboMensaje({ mensaje }: GloboMensajeProps) {
    return (
        <div
            className={`flex ${
                mensaje.esDelUsuarioPrincipal ? 'justify-end' : 'justify-start'
            } mb-4`}
        >
            <div className="flex flex-col gap-1">
                <div
                    className={`px-3 py-1.5 rounded-md inline-block w-fit ${
                        mensaje.esDelUsuarioPrincipal
                            ? 'bg-blue-500 text-white'
                            : 'bg-gray-300 text-gray-800'
                    }`}
                >
                    {mensaje.esImagen && mensaje.imagenUrl ? (
                        <img
                            src={mensaje.imagenUrl}
                            alt="Imagen compartida"
                            className="max-w-full h-auto rounded-sm max-h-80"
                        />
                    ) : (
                        <p
                            className={`text-sm md:text-base leading-normal ${
                                mensaje.esDelUsuarioPrincipal ? 'text-right' : 'text-left'
                            }`}
                        >
                            {mensaje.contenido}
                        </p>
                    )}
                </div>
                <p
                    className={`text-xs text-gray-500 ${
                        mensaje.esDelUsuarioPrincipal ? 'text-right' : 'text-left'
                    }`}
                >
                    {/*{mensaje.fecha} {extraerHora(mensaje.hora)}*/}
                    {formatearFechaHoraLocal(mensaje.fecha, mensaje.hora)}
                </p>
            </div>
        </div>
    );
}
