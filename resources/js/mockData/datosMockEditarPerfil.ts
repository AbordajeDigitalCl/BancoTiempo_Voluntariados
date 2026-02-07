export interface PerfilEditable {
    id: number;
    nombre: string;
    apellidoPaterno: string;
    apellidoMaterno: string;
    email: string;
    contrasena: string;
    confirmarContrasena: string;
    fechaNacimiento: string; // formato: YYYY-MM-DD
    telefono: string;
    direccion: string;
    ciudad: string;
}

// Datos precargados del usuario (datos ficticios)
export const mockPerfilEditableData: PerfilEditable = {
    id: 1,
    nombre: "Juan",
    apellidoPaterno: "Perez",
    apellidoMaterno: "García",
    email: "juan.perez@email.com",
    contrasena: "MiContraseña123",
    confirmarContrasena: "MiContraseña123",
    fechaNacimiento: "1985-03-15",
    telefono: "+56 9 1234 5678",
    direccion: "Avenida Principal 123, Piso 5",
    ciudad: "Santiago, Chile",
};
