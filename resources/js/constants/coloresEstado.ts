export const STATE_COLORS = {
  Activo: {
    border: 'border-l-green-500',
    icon: 'bg-green-500',
    text: 'text-green-500',
    hex: '#93C47D',
  },
  Desactivado: {
    border: 'border-l-blue-500',
    icon: 'bg-blue-500',
    text: 'text-blue-500',
    hex: '#6FA8DC',
  },
  Agotado: {
    border: 'border-l-red-500',
    icon: 'bg-red-500',
    text: 'text-red-500',
    hex: '#CC0000',
  },
} as const;

export type EstadoServicio = keyof typeof STATE_COLORS;

// Helper para obtener colores
export const getStateColors = (estado: EstadoServicio) => STATE_COLORS[estado];
