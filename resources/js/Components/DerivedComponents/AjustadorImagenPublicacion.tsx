import { useState, useRef, useEffect } from 'react';

interface AjustadorImagenProps {
  imagenFile: File;
  onConfirm: (croppedFile: File) => void;
  onCancel: () => void;
}

interface CropBox {
  x: number;
  y: number;
  size: number; // cuadrado
}

type ResizeMode = 'none' | 'move' | 'corner' | 'edge-left' | 'edge-right' | 'edge-top' | 'edge-bottom';

export default function AjustadorImagenPublicacion({ imagenFile, onConfirm, onCancel }: AjustadorImagenProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);
  const previewCanvasRef = useRef<HTMLCanvasElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);
  const [imagenCargada, setImagenCargada] = useState<HTMLImageElement | null>(null);
  const [cropBox, setCropBox] = useState<CropBox>({ x: 0, y: 0, size: 250 });
  const [resizeMode, setResizeMode] = useState<ResizeMode>('none');
  const [dragOffset, setDragOffset] = useState({ x: 0, y: 0 });
  const [isDraggingBox, setIsDraggingBox] = useState(false);
  const [isResizingBox, setIsResizingBox] = useState(false);
  const [isMouseDown, setIsMouseDown] = useState(false);

  // Cargar imagen
  useEffect(() => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.onload = () => {
        setImagenCargada(img);
        // Inicializar cropBox al centro
        const minDim = Math.min(img.width, img.height);
        const size = Math.min(250, minDim);
        setCropBox({
          x: (img.width - size) / 2,
          y: (img.height - size) / 2,
          size: size,
        });
      };
      img.src = e.target?.result as string;
    };
    reader.readAsDataURL(imagenFile);
  }, [imagenFile]);

  // Dibujar canvas principal
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas || !imagenCargada) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Calcular escala para que quepa en el canvas
    const maxWidth = 600;
    const maxHeight = 400;
    let displayWidth = imagenCargada.width;
    let displayHeight = imagenCargada.height;

    // Escalar para que quepa en el canvas
    const scaleX = maxWidth / displayWidth;
    const scaleY = maxHeight / displayHeight;
    const scale = Math.min(scaleX, scaleY, 1);

    displayWidth *= scale;
    displayHeight *= scale;

    canvas.width = displayWidth;
    canvas.height = displayHeight;

    // Dibujar imagen sin zoom (sin escala adicional)
    ctx.drawImage(imagenCargada, 0, 0, displayWidth, displayHeight);

    // Dibujar área oscura alrededor del crop box
    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
    ctx.fillRect(0, 0, displayWidth, displayHeight);

    // Área clara del crop box
    const scaleFactor = displayWidth / imagenCargada.width;
    const cropX = cropBox.x * scaleFactor;
    const cropY = cropBox.y * scaleFactor;
    const cropSize = cropBox.size * scaleFactor;

    //ctx.clearRect(cropX, cropY, cropSize, cropSize);
    ctx.drawImage(
    imagenCargada,
    cropBox.x,
    cropBox.y,
    cropBox.size,
    cropBox.size,
    cropX,
    cropY,
    cropSize,
    cropSize
    );

    // Borde blanco grueso del crop box
    ctx.strokeStyle = '#ffffff';
    ctx.lineWidth = 3;
    ctx.strokeRect(cropX, cropY, cropSize, cropSize);

    // Líneas grises de definición (rule of thirds)
    ctx.strokeStyle = 'rgba(128, 128, 128, 0.8)';
    ctx.lineWidth = 1;
    const tercio = cropSize / 3;
    for (let i = 1; i < 3; i++) {
      ctx.beginPath();
      ctx.moveTo(cropX + i * tercio, cropY);
      ctx.lineTo(cropX + i * tercio, cropY + cropSize);
      ctx.stroke();

      ctx.beginPath();
      ctx.moveTo(cropX, cropY + i * tercio);
      ctx.lineTo(cropX + cropSize, cropY + i * tercio);
      ctx.stroke();
    }

    // Pulsadores en las 4 esquinas para redimensionar
    const handleSize = 12;
    const handleColor = '#ffffff';
    
    // Esquinas (para redimensionar)
    const corners = [
      [cropX, cropY], // top-left
      [cropX + cropSize - handleSize, cropY], // top-right
      [cropX, cropY + cropSize - handleSize], // bottom-left
      [cropX + cropSize - handleSize, cropY + cropSize - handleSize], // bottom-right
    ];
    
    corners.forEach(([x, y]) => {
      ctx.fillStyle = handleColor;
      ctx.fillRect(x, y, handleSize, handleSize);
      ctx.strokeStyle = '#000000';
      ctx.lineWidth = 1;
      ctx.strokeRect(x, y, handleSize, handleSize);
    });

    // Líneas en las orillas (para indicar que se puede redimensionar desde aquí)
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.6)';
    ctx.lineWidth = 2;
    // Top edge
    ctx.beginPath();
    ctx.moveTo(cropX + handleSize, cropY);
    ctx.lineTo(cropX + cropSize - handleSize, cropY);
    ctx.stroke();
    // Bottom edge
    ctx.beginPath();
    ctx.moveTo(cropX + handleSize, cropY + cropSize);
    ctx.lineTo(cropX + cropSize - handleSize, cropY + cropSize);
    ctx.stroke();
    // Left edge
    ctx.beginPath();
    ctx.moveTo(cropX, cropY + handleSize);
    ctx.lineTo(cropX, cropY + cropSize - handleSize);
    ctx.stroke();
    // Right edge
    ctx.beginPath();
    ctx.moveTo(cropX + cropSize, cropY + handleSize);
    ctx.lineTo(cropX + cropSize, cropY + cropSize - handleSize);
    ctx.stroke();
  }, [imagenCargada, cropBox]);

  // Dibujar preview en vivo del encuadre
  useEffect(() => {
    const previewCanvas = previewCanvasRef.current;
    if (!previewCanvas || !imagenCargada) return;

    const ctx = previewCanvas.getContext('2d');
    if (!ctx) return;

    previewCanvas.width = 120;
    previewCanvas.height = 120;

    // Dibujar solo la parte que está dentro del crop box
    ctx.drawImage(
      imagenCargada,
      cropBox.x,
      cropBox.y,
      cropBox.size,
      cropBox.size,
      0,
      0,
      120,
      120
    );

    // Borde para que se vea bien el preview
    ctx.strokeStyle = '#cccccc';
    ctx.lineWidth = 1;
    ctx.strokeRect(0, 0, 120, 120);
  }, [imagenCargada, cropBox]);


  const handleMouseDown = (e: React.MouseEvent<HTMLCanvasElement>) => {
    if (!canvasRef.current || !imagenCargada) return;

    const canvas = canvasRef.current;
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    const maxWidth = 600;
    const maxHeight = 400;
    let displayWidth = imagenCargada.width;
    let displayHeight = imagenCargada.height;
    const scaleX = maxWidth / displayWidth;
    const scaleY = maxHeight / displayHeight;
    const scale = Math.min(scaleX, scaleY, 1);
    displayWidth *= scale;
    displayHeight *= scale;

    const scaleFactor = displayWidth / imagenCargada.width;
    const cropX = cropBox.x * scaleFactor;
    const cropY = cropBox.y * scaleFactor;
    const cropSize = cropBox.size * scaleFactor;
    const handleSize = 12;
    const edgeThreshold = 8; // píxeles dentro de la orilla para detectar resize

    // Detectar si tocó una esquina (resize mode: corner)
    const corners = [
      { x: cropX, y: cropY, mode: 'tl' as const },
      { x: cropX + cropSize, y: cropY, mode: 'tr' as const },
      { x: cropX, y: cropY + cropSize, mode: 'bl' as const },
      { x: cropX + cropSize, y: cropY + cropSize, mode: 'br' as const },
    ];

    for (const corner of corners) {
      if (
        mouseX >= corner.x - handleSize &&
        mouseX <= corner.x + handleSize &&
        mouseY >= corner.y - handleSize &&
        mouseY <= corner.y + handleSize
      ) {
        setResizeMode('corner');
        setIsResizingBox(true);
        setIsMouseDown(true);
        setDragOffset({ x: corner.x, y: corner.y });
        return;
      }
    }

    // Detectar si tocó una orilla (resize mode: edge)
    if (mouseY >= cropY - edgeThreshold && mouseY <= cropY + edgeThreshold && mouseX > cropX && mouseX < cropX + cropSize) {
      setResizeMode('edge-top');
      setIsResizingBox(true);
      setIsMouseDown(true);
      setDragOffset({ x: 0, y: cropY });
      return;
    }
    if (mouseY >= cropY + cropSize - edgeThreshold && mouseY <= cropY + cropSize + edgeThreshold && mouseX > cropX && mouseX < cropX + cropSize) {
      setResizeMode('edge-bottom');
      setIsResizingBox(true);
      setIsMouseDown(true);
      setDragOffset({ x: 0, y: cropY + cropSize });
      return;
    }
    if (mouseX >= cropX - edgeThreshold && mouseX <= cropX + edgeThreshold && mouseY > cropY && mouseY < cropY + cropSize) {
      setResizeMode('edge-left');
      setIsResizingBox(true);
      setIsMouseDown(true);
      setDragOffset({ x: cropX, y: 0 });
      return;
    }
    if (mouseX >= cropX + cropSize - edgeThreshold && mouseX <= cropX + cropSize + edgeThreshold && mouseY > cropY && mouseY < cropY + cropSize) {
      setResizeMode('edge-right');
      setIsResizingBox(true);
      setIsMouseDown(true);
      setDragOffset({ x: cropX + cropSize, y: 0 });
      return;
    }

    // Detectar si está en el interior (move mode)
    if (mouseX > cropX + handleSize && mouseX < cropX + cropSize - handleSize && mouseY > cropY + handleSize && mouseY < cropY + cropSize - handleSize) {
      setResizeMode('move');
      setIsDraggingBox(true);
      setIsMouseDown(true);
      setDragOffset({
        x: mouseX - cropX,
        y: mouseY - cropY,
      });
    }
  };

  const handleMouseMove = (e: React.MouseEvent<HTMLCanvasElement>) => {
    if (!canvasRef.current || !imagenCargada) return;

    const canvas = canvasRef.current;
    const rect = canvas.getBoundingClientRect();
    const mouseX = e.clientX - rect.left;
    const mouseY = e.clientY - rect.top;

    const maxWidth = 600;
    const maxHeight = 400;
    let displayWidth = imagenCargada.width;
    let displayHeight = imagenCargada.height;
    const scaleX = maxWidth / displayWidth;
    const scaleY = maxHeight / displayHeight;
    const scale = Math.min(scaleX, scaleY, 1);
    displayWidth *= scale;
    displayHeight *= scale;

    const scaleFactor = displayWidth / imagenCargada.width;
    const cropX = cropBox.x * scaleFactor;
    const cropY = cropBox.y * scaleFactor;
    const cropSize = cropBox.size * scaleFactor;
    const handleSize = 12;
    const edgeThreshold = 8;

    // Detectar cursor según la posición del mouse
    let cursorStyle = 'default';

    // Esquinas
    const corners = [
      { x: cropX, y: cropY },
      { x: cropX + cropSize, y: cropY },
      { x: cropX, y: cropY + cropSize },
      { x: cropX + cropSize, y: cropY + cropSize },
    ];

    for (const corner of corners) {
      if (
        mouseX >= corner.x - handleSize &&
        mouseX <= corner.x + handleSize &&
        mouseY >= corner.y - handleSize &&
        mouseY <= corner.y + handleSize
      ) {
        cursorStyle = 'nwse-resize';
        break;
      }
    }

    // Orillas
    if (cursorStyle === 'default') {
      if (mouseY >= cropY - edgeThreshold && mouseY <= cropY + edgeThreshold && mouseX > cropX && mouseX < cropX + cropSize) {
        cursorStyle = 'ns-resize';
      } else if (mouseY >= cropY + cropSize - edgeThreshold && mouseY <= cropY + cropSize + edgeThreshold && mouseX > cropX && mouseX < cropX + cropSize) {
        cursorStyle = 'ns-resize';
      } else if (mouseX >= cropX - edgeThreshold && mouseX <= cropX + edgeThreshold && mouseY > cropY && mouseY < cropY + cropSize) {
        cursorStyle = 'ew-resize';
      } else if (mouseX >= cropX + cropSize - edgeThreshold && mouseX <= cropX + cropSize + edgeThreshold && mouseY > cropY && mouseY < cropY + cropSize) {
        cursorStyle = 'ew-resize';
      }
    }

    // Centro (move)
    if (mouseX > cropX + handleSize && mouseX < cropX + cropSize - handleSize && mouseY > cropY + handleSize && mouseY < cropY + cropSize - handleSize) {
      cursorStyle = 'move';
    }

    canvas.style.cursor = cursorStyle;

    // Solo aplicar cambios si el mouse está presionado
    if (resizeMode === 'none' || !isMouseDown) return;

    const minSize = 50;

    if (resizeMode === 'move') {
      // Mover el crop box
      let newX = (mouseX - dragOffset.x) / scaleFactor;
      let newY = (mouseY - dragOffset.y) / scaleFactor;

      newX = Math.max(0, Math.min(newX, imagenCargada.width - cropBox.size));
      newY = Math.max(0, Math.min(newY, imagenCargada.height - cropBox.size));

      setCropBox((prev) => ({ ...prev, x: newX, y: newY }));
    } else if (resizeMode === 'corner') {
      // Redimensionar desde esquina - cambiar tanto posición como tamaño
      const currentX = dragOffset.x / scaleFactor;
      const currentY = dragOffset.y / scaleFactor;
      const newMouseX = mouseX / scaleFactor;
      const newMouseY = mouseY / scaleFactor;

      const newSize = Math.max(minSize, Math.abs(newMouseX - (cropBox.x + cropBox.size / 2)) * 2, Math.abs(newMouseY - (cropBox.y + cropBox.size / 2)) * 2);
      const newX = Math.max(0, Math.min(cropBox.x, imagenCargada.width - newSize));
      const newY = Math.max(0, Math.min(cropBox.y, imagenCargada.height - newSize));

      setCropBox({ x: newX, y: newY, size: newSize });
    } else if (resizeMode === 'edge-bottom') {
      // Expandir/contraer desde abajo
      const newMouseY = mouseY / scaleFactor;
      const newSize = Math.max(minSize, newMouseY - cropBox.y);
      const maxSize = imagenCargada.height - cropBox.y;
      setCropBox((prev) => ({ ...prev, size: Math.min(newSize, maxSize) }));
    } else if (resizeMode === 'edge-top') {
      // Expandir/contraer desde arriba
      const newMouseY = mouseY / scaleFactor;
      const newSize = Math.max(minSize, cropBox.y + cropBox.size - newMouseY);
      const newY = Math.max(0, cropBox.y + cropBox.size - newSize);
      setCropBox({ ...cropBox, y: newY, size: newSize });
    } else if (resizeMode === 'edge-right') {
      // Expandir/contraer desde la derecha
      const newMouseX = mouseX / scaleFactor;
      const newSize = Math.max(minSize, newMouseX - cropBox.x);
      const maxSize = imagenCargada.width - cropBox.x;
      setCropBox((prev) => ({ ...prev, size: Math.min(newSize, maxSize) }));
    } else if (resizeMode === 'edge-left') {
      // Expandir/contraer desde la izquierda
      const newMouseX = mouseX / scaleFactor;
      const newSize = Math.max(minSize, cropBox.x + cropBox.size - newMouseX);
      const newX = Math.max(0, cropBox.x + cropBox.size - newSize);
      setCropBox({ ...cropBox, x: newX, size: newSize });
    }
  };

  const handleMouseUp = () => {
    setIsMouseDown(false);
    setIsDraggingBox(false);
    setIsResizingBox(false);
    setResizeMode('none');
  };

  const handleConfirm = async () => {
    if (!imagenCargada) return;

    // Crear canvas para el recorte
    const cropCanvas = document.createElement('canvas');
    cropCanvas.width = cropBox.size;
    cropCanvas.height = cropBox.size;

    const ctx = cropCanvas.getContext('2d');
    if (!ctx) return;

    ctx.drawImage(
      imagenCargada,
      cropBox.x,
      cropBox.y,
      cropBox.size,
      cropBox.size,
      0,
      0,
      cropBox.size,
      cropBox.size
    );

    

    // Convertir canvas a File
    cropCanvas.toBlob((blob) => {
      if (blob) {
        const croppedFile = new File([blob], imagenFile.name, { type: 'image/jpeg' });
        onConfirm(croppedFile);
      }
    }, 'image/jpeg', 0.95);
  };



  return (
    <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto">
        {/* Header */}
        <div className="bg-gray-100 px-6 py-4 border-b border-gray-200">
          <h2 className="text-2xl font-bold text-gray-800">Ajustar imagen</h2>
          <p className="text-sm text-gray-600 mt-1">
            Arrastra el cuadro blanco desde el centro para mover la imagen. Redimensiona desde las orillas o esquinas. El área blanca es lo que se verá en tu publicación.
          </p>
        </div>

        {/* Contenido principal */}
        <div className="p-6">
          <div className="grid grid-cols-3 gap-6">
            {/* Canvas area (izquierda) */}
            <div className="col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-2">Ajuste y encuadre</label>
              <div className="flex justify-center mb-4 bg-gray-50 rounded-lg p-2 border border-gray-300">
                <canvas
                  ref={canvasRef}
                  onMouseDown={handleMouseDown}
                  onMouseMove={handleMouseMove}
                  onMouseUp={handleMouseUp}
                  onMouseLeave={handleMouseUp}
                  className={`${isDraggingBox || isResizingBox ? 'cursor-grabbing' : 'cursor-grab'} rounded`}
                  style={{ display: 'block', maxWidth: '100%' }}
                />
              </div>
            </div>

            {/* Preview area (derecha) */}
            <div className="col-span-1">
              <label className="block text-sm font-medium text-gray-700 mb-2">Vista previa final</label>
              <div className="bg-gray-100 p-3 rounded-lg border-2 border-gray-300 flex flex-col items-center">
                <div className="mb-2 flex justify-center bg-white p-1 rounded border border-gray-300">
                  <canvas
                    ref={previewCanvasRef}
                    className="rounded"
                  />
                </div>
                <p className="text-xs text-gray-700 font-mono text-center">
                  Así se verá<br/>en tu publicación
                </p>
                <p className="text-xs text-gray-500 mt-2">
                  Tamaño: {Math.round(cropBox.size)}×{Math.round(cropBox.size)} px
                </p>
              </div>
            </div>
          </div>
        </div>

        {/* Footer with buttons */}
        <div className="bg-gray-50 px-6 py-4 border-t border-gray-200 flex gap-3 justify-end">
          <button
            onClick={onCancel}
            className="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium rounded-lg transition"
          >
            Cancelar
          </button>
          <button
            onClick={handleConfirm}
            className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition"
          >
            Confirmar
          </button>
        </div>
      </div>
    </div>
  );
}
