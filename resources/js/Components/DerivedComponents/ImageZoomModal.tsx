import { useState } from 'react';

interface ImageZoomModalProps {
    src: string;
    alt: string;
    className?: string;
}

export default function ImageZoomModal({ src, alt, className = '' }: ImageZoomModalProps) {
    const [isZoomed, setIsZoomed] = useState(false);

    const handleClose = () => {
        setIsZoomed(false);
    };

    return (
        <>
            <style>{`
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                    }
                    to {
                        opacity: 1;
                    }
                }
                .zoom-modal-overlay {
                    animation: fadeIn 0.3s ease-in-out;
                }
            `}</style>

         
            <img
                src={src}
                alt={alt}
                className={`cursor-pointer hover:opacity-90 transition-opacity ${className}`}
                onClick={() => setIsZoomed(true)}
            />

           
            {isZoomed && (
                <div
                    className="zoom-modal-overlay fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center"
                    onClick={handleClose}
                >
                    <div
                        className="relative w-full max-w-sm md:max-w-2xl h-auto"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <img
                            src={src}
                            alt={alt}
                            className="w-full h-auto max-h-screen object-contain rounded-lg"
                            onClick={handleClose}
                        />
                    </div>
                </div>
            )}
        </>
    );
}
