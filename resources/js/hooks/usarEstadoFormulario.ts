import { useState } from 'react';
import { PerfilEditable } from '@/mockData/datosMockEditarPerfil';

interface UseFormStateReturn {
    formData: PerfilEditable;
    mostrarContrasena: boolean;
    mostrarConfirmar: boolean;
    handleChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    handleTogglePassword: () => void;
    handleToggleConfirm: () => void;
    handleSubmit: (callback: (data: PerfilEditable) => void) => (e: React.FormEvent<HTMLFormElement>) => void;
}

export function useFormState(datosIniciales: PerfilEditable): UseFormStateReturn {
    const [formData, setFormData] = useState<PerfilEditable>(datosIniciales);
    const [mostrarContrasena, setMostrarContrasena] = useState(false);
    const [mostrarConfirmar, setMostrarConfirmar] = useState(false);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleTogglePassword = () => {
        setMostrarContrasena(!mostrarContrasena);
    };

    const handleToggleConfirm = () => {
        setMostrarConfirmar(!mostrarConfirmar);
    };

    const handleSubmit = (callback: (data: PerfilEditable) => void) => (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        callback(formData);
    };

    return {
        formData,
        mostrarContrasena,
        mostrarConfirmar,
        handleChange,
        handleTogglePassword,
        handleToggleConfirm,
        handleSubmit
    };
}
