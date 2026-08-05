import { useState, useEffect } from 'react';

export const useDateTime = () => { // pastikan nama export-nya useDateTime (kecil)
  const [dateTime, setDateTime] = useState(new Date());

  useEffect(() => {
    const timer = setInterval(() => setDateTime(new Date()), 1000);
    return () => clearInterval(timer);
  }, []);

  const formatter = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  });

  return formatter.format(dateTime).replace(/\./g, ':');
};