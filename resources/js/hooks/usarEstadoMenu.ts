import { useState } from 'react';

export const useMenuState = () => {
  const [menuAbierto, setMenuAbierto] = useState(false);

  const toggleMenu = () => {
    setMenuAbierto((prev) => !prev);
  };

  const closeMenu = () => {
    setMenuAbierto(false);
  };

  const openMenu = () => {
    setMenuAbierto(true);
  };

  return {
    menuAbierto,
    toggleMenu,
    closeMenu,
    openMenu,
  };
};
