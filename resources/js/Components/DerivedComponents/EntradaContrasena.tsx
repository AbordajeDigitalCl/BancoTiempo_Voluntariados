interface PasswordInputProps {
    name: string;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    mostrarPassword: boolean;
    onToggle: () => void;
    label: string;
    placeholder: string;
}

export default function PasswordInput({
    name,
    value,
    onChange,
    mostrarPassword,
    onToggle,
    label,
    placeholder
}: PasswordInputProps) {
    return (
        <div>
            <label className="block text-sm font-bold text-gray-700 mb-2">
                {label}
            </label>
            <div className="relative">
                <input
                    type={mostrarPassword ? "text" : "password"}
                    name={name}
                    value={value}
                    onChange={onChange}
                    className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder={placeholder}
                />
                <button
                    type="button"
                    onClick={onToggle}
                    className="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700"
                    title={mostrarPassword ? "Ocultar contraseña" : "Mostrar contraseña"}
                >
                    {mostrarPassword ? (
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    ) : (
                        <svg xmlns="http://www.w3.org/2000/svg" className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M1 1l22 22" />
                        </svg>
                    )}
                </button>
            </div>
        </div>
    );
}
