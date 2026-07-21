import React, { useState, useEffect, useRef, useMemo } from 'react';

// ==========================================
// MOCK DATABASE & LOCAL STORAGE SIMULATION
// ==========================================

const INITIAL_CLINICAS = [
  { id: 'cli-1', NOMBRE: 'FG Neuro Centro', DIRECCION: 'Av. Paulista, 1000 - São Paulo', TELEFONO: '(11) 3222-1100', EMAIL: 'centro@fgneurologia.com.br' },
  { id: 'cli-2', NOMBRE: 'Clínica Neurológica Paulista', DIRECCION: 'Alameda Lorena, 450 - Jardins', TELEFONO: '(11) 3888-2211', EMAIL: 'paulista@neuro.com.br' },
  { id: 'cli-3', NOMBRE: 'NeuroVila Resonância', DIRECCION: 'Rua Mourato Coelho, 820 - Pinheiros', TELEFONO: '(11) 3031-4400', EMAIL: 'contato@neurovila.com.br' },
  { id: 'cli-4', NOMBRE: 'Cérebro & Vida Porto Alegre', DIRECCION: 'Av. Nilo Peçanha, 1500 - Três Figueiras', TELEFONO: '(51) 3330-9988', EMAIL: 'poa@cerebrovida.com.br' }
];

const INITIAL_USUARIOS = [
  { id: 'usr-1', USUARIO: 'clinica1', PASSWORD: '123', NOMBRES: 'Carlos', APELLIDOS: 'Melo', ID_CLINICA: 'cli-1' },
  { id: 'usr-2', USUARIO: 'clinica2', PASSWORD: '123', NOMBRES: 'Fernanda', APELLIDOS: 'Souza', ID_CLINICA: 'cli-2' },
  { id: 'usr-3', USUARIO: 'clinica3', PASSWORD: '123', NOMBRES: 'Guilherme', APELLIDOS: 'Teixeira', ID_CLINICA: 'cli-3' }
];

const INITIAL_ADMINISTRADORES = [
  { id: 'adm-1', USUARIO: 'admin', PASSWORD: '123', NOMBRES: 'Dr. Francisco', APELLIDOS: 'Gomez' },
  { id: 'adm-2', USUARIO: 'admin2', PASSWORD: '123', NOMBRES: 'Dra. Beatris', APELLIDOS: 'Gomes' }
];

const INITIAL_LAUDOS = [
  { id: 'lau-1', id_documento: 'DOC-90812', DOCUMENTO: '112.345.678-01', NOMBRES: 'Ana Maria Silva', FECHA_ESTUDIO: '2026-05-15', TIPO_ESTUDIO: 'Eletroencefalograma (EEG)', ID_CLINICA: 'cli-1' },
  { id: 'lau-2', id_documento: 'DOC-77421', DOCUMENTO: '223.456.789-02', NOMBRES: 'João Pedro Santos', FECHA_ESTUDIO: '2026-05-20', TIPO_ESTUDIO: 'Polissonografia', ID_CLINICA: 'cli-1' },
  { id: 'lau-3', id_documento: 'DOC-44122', DOCUMENTO: '334.567.890-03', NOMBRES: 'Mariana Costa Ribeiro', FECHA_ESTUDIO: '2026-06-01', TIPO_ESTUDIO: 'Eletroneuromiografia (ENMG)', ID_CLINICA: 'cli-2' },
  { id: 'lau-4', id_documento: 'DOC-55110', DOCUMENTO: '445.678.901-04', NOMBRES: 'Roberto Oliveira de Paula', FECHA_ESTUDIO: '2026-05-10', TIPO_ESTUDIO: 'Mapeamento Cerebral', ID_CLINICA: 'cli-2' },
  { id: 'lau-5', id_documento: 'DOC-88992', DOCUMENTO: '556.789.012-05', NOMBRES: 'Beatriz Pinheiro Lima', FECHA_ESTUDIO: '2026-05-28', TIPO_ESTUDIO: 'Potencial Evocado', ID_CLINICA: 'cli-3' },
  { id: 'lau-6', id_documento: 'DOC-12345', DOCUMENTO: '667.890.123-06', NOMBRES: 'Carlos Eduardo Nogueira', FECHA_ESTUDIO: '2026-06-02', TIPO_ESTUDIO: 'Eletroencefalograma (EEG)', ID_CLINICA: 'cli-3' },
  { id: 'lau-7', id_documento: 'DOC-65432', DOCUMENTO: '778.901.234-07', NOMBRES: 'Juliana Lima Medeiros', FECHA_ESTUDIO: '2026-04-18', TIPO_ESTUDIO: 'Polissonografia', ID_CLINICA: 'cli-4' },
  { id: 'lau-8', id_documento: 'DOC-98765', DOCUMENTO: '889.012.345-08', NOMBRES: 'Marcos Rocha Tavares', FECHA_ESTUDIO: '2026-05-02', TIPO_ESTUDIO: 'Eletroneuromiografia (ENMG)', ID_CLINICA: 'cli-4' },
  { id: 'lau-9', id_documento: 'DOC-45678', DOCUMENTO: '990.123.456-09', NOMBRES: 'Patrícia Albuquerque', FECHA_ESTUDIO: '2026-05-11', TIPO_ESTUDIO: 'Mapeamento Cerebral', ID_CLINICA: 'cli-1' }
];

// ==========================================
// ICONOS SVG CUSTOMIZADOS (Reemplazo de Lucide robusto)
// ==========================================

const Icons = {
  Brain: ({ className = "w-6 h-6" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.467 5.99 5.99 0 0 0-1.925 3.546 5.974 5.974 0 0 1-2.133-1A3.75 3.75 0 0 0 12 18Z" />
      <path strokeLinecap="round" strokeLinejoin="round" d="M9.75 15H12v2.25M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 19.4a.75.75 0 0 0 1.06 1.06l1.79-1.79A8.952 8.952 0 0 0 12 21c4.97 0 9-4.03 9-9s-4.03-9-9-9Zm0 15a6 6 0 1 1 0-12 6 6 0 0 1 0 12Z" />
    </svg>
  ),
  User: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
    </svg>
  ),
  Lock: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
  ),
  Layers: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m11.142 0L21.75 12l-4.179-2.25M12 5.25 15.75 7.5 12 9.75 8.25 7.5 12 5.25Zm0 11.25L15.75 14.25 12 16.5l-3.75-2.25L12 16.5Z" />
    </svg>
  ),
  LogOut: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
    </svg>
  ),
  Search: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.608 10.608Z" />
    </svg>
  ),
  Calendar: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
    </svg>
  ),
  Funnel: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
    </svg>
  ),
  XMark: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
    </svg>
  ),
  Eye: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
      <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>
  ),
  Download: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
    </svg>
  ),
  Trash: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
    </svg>
  ),
  Building: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.053.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
    </svg>
  ),
  Users: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
    </svg>
  ),
  Plus: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
  ),
  DocumentText: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
    </svg>
  ),
  Pencil: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.52 4.52 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
    </svg>
  ),
  Warning: ({ className = "w-8 h-8 text-red-500" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
    </svg>
  ),
  Check: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="m4.5 12.75 6 6 9-13.5" />
    </svg>
  ),
  ChevronLeft: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
    </svg>
  ),
  ChevronRight: ({ className = "w-5 h-5" }) => (
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="2" stroke="currentColor" className={className}>
      <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
    </svg>
  )
};

// ==========================================
// COMPONENTE: NeuralBackground (Canvas Animado)
// ==========================================

const NeuralBackground = () => {
  const canvasRef = useRef(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    let animationFrameId;

    const resizeCanvas = () => {
      canvas.width = canvas.parentElement.offsetWidth;
      canvas.height = canvas.parentElement.offsetHeight;
    };

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Configuración de partículas (30 nodos estipulados)
    const particleCount = 30;
    const particles = [];

    for (let i = 0; i < particleCount; i++) {
      particles.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        vx: (Math.random() - 0.5) * 0.6,
        vy: (Math.random() - 0.5) * 0.6,
        radius: Math.random() * 3 + 2,
        pulseSpeed: 0.02 + Math.random() * 0.03,
        pulseValue: Math.random() * Math.PI
      });
    }

    const draw = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      // Dibujar conexiones primero (líneas con opacidad)
      for (let i = 0; i < particleCount; i++) {
        const p1 = particles[i];
        for (let j = i + 1; j < particleCount; j++) {
          const p2 = particles[j];
          const dist = Math.hypot(p1.x - p2.x, p1.y - p2.y);
          // Distancia máxima de conexión (25% de la dimensión menor o 200px)
          const maxDist = Math.min(canvas.width, canvas.height) * 0.25;

          if (dist < maxDist) {
            const alpha = (1 - dist / maxDist) * 0.15;
            ctx.beginPath();
            ctx.moveTo(p1.x, p1.y);
            ctx.lineTo(p2.x, p2.y);
            ctx.strokeStyle = `rgba(26, 159, 201, ${alpha})`;
            ctx.lineWidth = 1;
            ctx.stroke();
          }
        }
      }

      // Dibujar y actualizar partículas
      particles.forEach((p) => {
        p.x += p.vx;
        p.y += p.vy;

        // Rebotar en bordes
        if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
        if (p.y < 0 || p.y > canvas.height) p.vy *= -1;

        // Efecto pulsante de los nodos
        p.pulseValue += p.pulseSpeed;
        const scale = 1 + Math.sin(p.pulseValue) * 0.4;
        const opacity = 0.4 + (Math.sin(p.pulseValue) + 1) * 0.3; // opacity 0.1 to 0.7 aprox

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.radius * scale, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(6, 182, 212, ${opacity})`; // Cyan glow
        ctx.shadowColor = 'rgba(6, 182, 212, 0.5)';
        ctx.shadowBlur = 8;
        ctx.fill();
        ctx.shadowBlur = 0; // Reset
      });

      animationFrameId = requestAnimationFrame(draw);
    };

    draw();

    return () => {
      window.removeEventListener('resize', resizeCanvas);
      cancelAnimationFrame(animationFrameId);
    };
  }, []);

  return <canvas ref={canvasRef} className="absolute inset-0 w-full h-full pointer-events-none z-0" />;
};

// ==========================================
// COMPONENTE: TOASTS (Notificaciones)
// ==========================================

let toastIdCounter = 0;
let globalAddToast = () => {};

export const toast = {
  success: (msg) => globalAddToast(msg, 'success'),
  error: (msg) => globalAddToast(msg, 'error'),
  info: (msg) => globalAddToast(msg, 'info')
};

const ToastContainer = () => {
  const [toasts, setToasts] = useState([]);

  useEffect(() => {
    globalAddToast = (message, type) => {
      const id = toastIdCounter++;
      setToasts(prev => [...prev, { id, message, type }]);
      setTimeout(() => {
        setToasts(prev => prev.filter(t => t.id !== id));
      }, 4000);
    };
  }, []);

  return (
    <div className="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
      {toasts.map(t => (
        <div
          key={t.id}
          className={`pointer-events-auto p-4 rounded-xl shadow-lg border flex items-center gap-3 animate-fade-in-up transition-all duration-300 ${
            t.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
            t.type === 'error' ? 'bg-red-50 border-red-200 text-red-800' :
            'bg-slate-50 border-slate-200 text-slate-800'
          }`}
        >
          {t.type === 'success' && <Icons.Check className="w-5 h-5 text-emerald-600 shrink-0" />}
          {t.type === 'error' && <Icons.XMark className="w-5 h-5 text-red-600 shrink-0" />}
          <span className="text-sm font-medium">{t.message}</span>
        </div>
      ))}
    </div>
  );
};

// ==========================================
// COMPONENTE PRINCIPAL (App)
// ==========================================

export default function App() {
  // --- Estados de Rutas ---
  // 'login' | 'login-admin' | 'dashboard' | 'dashboard-admin' | 'ver-pdf'
  const [currentRoute, setCurrentRoute] = useState('login');
  const [routeParams, setRouteParams] = useState({});

  // --- Estados de Base de Datos Virtual (Carga desde LocalStorage si existe o inicializa)
  const [clinicas, setClinicas] = useState(() => {
    const saved = localStorage.getItem('fg_clinicas');
    return saved ? JSON.parse(saved) : INITIAL_CLINICAS;
  });

  const [usuarios, setUsuarios] = useState(() => {
    const saved = localStorage.getItem('fg_usuarios');
    return saved ? JSON.parse(saved) : INITIAL_USUARIOS;
  });

  const [administradores, setAdministradores] = useState(() => {
    const saved = localStorage.getItem('fg_administradores');
    return saved ? JSON.parse(saved) : INITIAL_ADMINISTRADORES;
  });

  const [laudos, setLaudos] = useState(() => {
    const saved = localStorage.getItem('fg_laudos');
    return saved ? JSON.parse(saved) : INITIAL_LAUDOS;
  });

  // --- Persistencia automática en LocalStorage ---
  useEffect(() => {
    localStorage.setItem('fg_clinicas', JSON.stringify(clinicas));
  }, [clinicas]);

  useEffect(() => {
    localStorage.setItem('fg_usuarios', JSON.stringify(usuarios));
  }, [usuarios]);

  useEffect(() => {
    localStorage.setItem('fg_administradores', JSON.stringify(administradores));
  }, [administradores]);

  useEffect(() => {
    localStorage.setItem('fg_laudos', JSON.stringify(laudos));
  }, [laudos]);

  // --- Estado de Sesión Actual ---
  const [currentUser, setCurrentUser] = useState(() => {
    const saved = localStorage.getItem('fg_session');
    return saved ? JSON.parse(saved) : null;
  });

  const handleLogin = (user, type) => {
    const sessionData = { ...user, sessionType: type };
    setCurrentUser(sessionData);
    localStorage.setItem('fg_session', JSON.stringify(sessionData));
    if (type === 'admin') {
      setCurrentRoute('dashboard-admin');
      toast.success(`Bem-vindo, Administrador ${user.NOMBRES}!`);
    } else {
      setCurrentRoute('dashboard');
      toast.success(`Bem-vindo de volta, ${user.NOMBRES}!`);
    }
  };

  const handleLogout = () => {
    setCurrentUser(null);
    localStorage.removeItem('fg_session');
    setCurrentRoute('login');
    toast.info('Sessão encerrada com sucesso.');
  };

  // --- Filtros de Búsqueda ---
  const [filters, setFilters] = useState({
    search: '',
    clinicaId: 'ALL',
    fechaInicio: '',
    fechaFin: ''
  });

  const [tempFilters, setTempFilters] = useState({
    search: '',
    clinicaId: 'ALL',
    fechaInicio: '',
    fechaFin: ''
  });

  const handleApplyFilters = () => {
    setFilters({ ...tempFilters });
    setCurrentPage(1);
    toast.success('Filtros aplicados.');
  };

  const handleClearFilters = () => {
    const cleared = { search: '', clinicaId: 'ALL', fechaInicio: '', fechaFin: '' };
    setTempFilters(cleared);
    setFilters(cleared);
    setCurrentPage(1);
    toast.info('Filtros limpos.');
  };

  // Verificar si hay filtros activos
  const hasActiveFilters = useMemo(() => {
    return filters.search !== '' || filters.clinicaId !== 'ALL' || filters.fechaInicio !== '' || filters.fechaFin !== '';
  }, [filters]);

  // --- Paginación ---
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 5;

  // --- Filtrado de Laudos ---
  const filteredLaudos = useMemo(() => {
    return laudos.filter(laudo => {
      // Filtrar por clínica de sesión si es usuario clínico normal
      if (currentUser?.sessionType === 'user' && laudo.ID_CLINICA !== currentUser.ID_CLINICA) {
        return false;
      }
      // Filtro de clínica si es administrador
      if (currentUser?.sessionType === 'admin' && filters.clinicaId !== 'ALL' && laudo.ID_CLINICA !== filters.clinicaId) {
        return false;
      }
      // Filtro de texto de búsqueda (paciente o documento de estudio o código)
      if (filters.search) {
        const query = filters.search.toLowerCase();
        const matchesPatient = laudo.NOMBRES.toLowerCase().includes(query);
        const matchesDoc = laudo.DOCUMENTO.toLowerCase().includes(query);
        const matchesIdDoc = laudo.id_documento.toLowerCase().includes(query);
        const matchesType = laudo.TIPO_ESTUDIO.toLowerCase().includes(query);
        if (!matchesPatient && !matchesDoc && !matchesIdDoc && !matchesType) return false;
      }
      // Filtro de fecha inicio
      if (filters.fechaInicio && laudo.FECHA_ESTUDIO < filters.fechaInicio) {
        return false;
      }
      // Filtro de fecha fin
      if (filters.fechaFin && laudo.FECHA_ESTUDIO > filters.fechaFin) {
        return false;
      }
      return true;
    }).sort((a, b) => b.FECHA_ESTUDIO.localeCompare(a.FECHA_ESTUDIO));
  }, [laudos, currentUser, filters]);

  // Laudos Paginados
  const paginatedLaudos = useMemo(() => {
    const startIndex = (currentPage - 1) * itemsPerPage;
    return filteredLaudos.slice(startIndex, startIndex + itemsPerPage);
  }, [filteredLaudos, currentPage]);

  const totalPages = Math.ceil(filteredLaudos.length / itemsPerPage) || 1;

  // --- Tabs del Administrador ---
  // 'laudos' | 'clinicas' | 'usuarios' | 'admins'
  const [activeAdminTab, setActiveAdminTab] = useState('laudos');

  // --- CONTROL DE MODALES ---
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
  const [deleteTarget, setDeleteTarget] = useState(null); // { type: 'laudo'|'clinica'|'usuario'|'admin', data: Object }

  const [isCRUDModalOpen, setIsCRUDModalOpen] = useState(false);
  const [crudAction, setCrudAction] = useState({ type: 'create', entity: '', data: null }); // type: 'create'|'edit', entity: 'laudo'|'clinica'|'usuario'|'admin'

  // Controlador de tecla Escape para cerrar modales
  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === 'Escape') {
        setIsDeleteModalOpen(false);
        setIsCRUDModalOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  // Cerrar modales con click fuera (backdrop)
  const handleBackdropClick = (e, closeFn) => {
    if (e.target === e.currentTarget) {
      closeFn(false);
    }
  };

  // --- LÓGICA DE ELIMINACIÓN ---
  const triggerDelete = (type, data) => {
    setDeleteTarget({ type, data });
    setIsDeleteModalOpen(true);
  };

  const confirmDelete = () => {
    if (!deleteTarget) return;
    const { type, data } = deleteTarget;

    if (type === 'laudo') {
      setLaudos(prev => prev.filter(l => l.id !== data.id));
      toast.success(`Laudo de ${data.NOMBRES} excluído com sucesso.`);
    } else if (type === 'clinica') {
      // Verificar si hay laudos o usuarios vinculados
      const hasLaudos = laudos.some(l => l.ID_CLINICA === data.id);
      const hasUsers = usuarios.some(u => u.ID_CLINICA === data.id);
      if (hasLaudos || hasUsers) {
        toast.error(`Não é possível excluir a clínica. Existem laudos ou usuários vinculados.`);
        setIsDeleteModalOpen(false);
        return;
      }
      setClinicas(prev => prev.filter(c => c.id !== data.id));
      toast.success(`Clínica "${data.NOMBRE}" excluída.`);
    } else if (type === 'usuario') {
      setUsuarios(prev => prev.filter(u => u.id !== data.id));
      toast.success(`Usuário clínico "${data.USUARIO}" excluído.`);
    } else if (type === 'admin') {
      if (administradores.length <= 1) {
        toast.error('Não é possível excluir o único administrador do sistema.');
        setIsDeleteModalOpen(false);
        return;
      }
      setAdministradores(prev => prev.filter(a => a.id !== data.id));
      toast.success(`Administrador "${data.USUARIO}" excluído.`);
    }

    setIsDeleteModalOpen(false);
    setDeleteTarget(null);
  };

  // --- LÓGICA DE CRUD (Crear & Editar) ---
  const triggerCreate = (entity) => {
    setCrudAction({ type: 'create', entity, data: null });
    setIsCRUDModalOpen(true);
  };

  const triggerEdit = (entity, data) => {
    setCrudAction({ type: 'edit', entity, data });
    setIsCRUDModalOpen(true);
  };

  const handleCRUDSave = (formData) => {
    const { type, entity, data } = crudAction;

    if (entity === 'clinica') {
      if (type === 'create') {
        const newClinica = {
          id: `cli-${Date.now()}`,
          NOMBRE: formData.NOMBRE,
          DIRECCION: formData.DIRECCION || '',
          TELEFONO: formData.TELEFONO || '',
          EMAIL: formData.EMAIL || ''
        };
        setClinicas(prev => [...prev, newClinica]);
        toast.success(`Clínica "${formData.NOMBRE}" adicionada com sucesso.`);
      } else {
        setClinicas(prev => prev.map(c => c.id === data.id ? { ...c, ...formData } : c));
        toast.success(`Clínica "${formData.NOMBRE}" atualizada.`);
      }
    }

    else if (entity === 'usuario') {
      if (type === 'create') {
        // Validación de duplicado
        if (usuarios.some(u => u.USUARIO === formData.USUARIO)) {
          toast.error('Este nome de usuário clínico já existe.');
          return;
        }
        const newUser = {
          id: `usr-${Date.now()}`,
          USUARIO: formData.USUARIO,
          PASSWORD: formData.PASSWORD || '123',
          NOMBRES: formData.NOMBRES,
          APELLIDOS: formData.APELLIDOS,
          ID_CLINICA: formData.ID_CLINICA
        };
        setUsuarios(prev => [...prev, newUser]);
        toast.success(`Usuário "${formData.USUARIO}" criado com sucesso.`);
      } else {
        setUsuarios(prev => prev.map(u => u.id === data.id ? {
          ...u,
          NOMBRES: formData.NOMBRES,
          APELLIDOS: formData.APELLIDOS,
          ID_CLINICA: formData.ID_CLINICA,
          PASSWORD: formData.PASSWORD ? formData.PASSWORD : u.PASSWORD
        } : u));
        toast.success(`Usuário clínico atualizado.`);
      }
    }

    else if (entity === 'admin') {
      if (type === 'create') {
        if (administradores.some(a => a.USUARIO === formData.USUARIO)) {
          toast.error('Este nome de usuário administrador já está em uso.');
          return;
        }
        const newAdmin = {
          id: `adm-${Date.now()}`,
          USUARIO: formData.USUARIO,
          PASSWORD: formData.PASSWORD || '123',
          NOMBRES: formData.NOMBRES,
          APELLIDOS: formData.APELLIDOS
        };
        setAdministradores(prev => [...prev, newAdmin]);
        toast.success(`Administrador "${formData.USUARIO}" criado com sucesso.`);
      } else {
        setAdministradores(prev => prev.map(a => a.id === data.id ? {
          ...a,
          NOMBRES: formData.NOMBRES,
          APELLIDOS: formData.APELLIDOS,
          PASSWORD: formData.PASSWORD ? formData.PASSWORD : a.PASSWORD
        } : a));
        toast.success(`Dados do administrador atualizados.`);
      }
    }

    else if (entity === 'laudo') {
      if (type === 'create') {
        const newLaudo = {
          id: `lau-${Date.now()}`,
          id_documento: `DOC-${Math.floor(10000 + Math.random() * 90000)}`,
          DOCUMENTO: formData.DOCUMENTO,
          NOMBRES: formData.NOMBRES,
          FECHA_ESTUDIO: formData.FECHA_ESTUDIO,
          TIPO_ESTUDIO: formData.TIPO_ESTUDIO,
          ID_CLINICA: formData.ID_CLINICA
        };
        setLaudos(prev => [...prev, newLaudo]);
        toast.success(`Laudo de ${formData.NOMBRES} inserido.`);
      } else {
        setLaudos(prev => prev.map(l => l.id === data.id ? { ...l, ...formData } : l));
        toast.success(`Laudo de ${formData.NOMBRES} atualizado.`);
      }
    }

    setIsCRUDModalOpen(false);
  };

  return (
    <div className="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased selection:bg-cyan-500 selection:text-white">
      
      {/* Contenedor de Toasts Global */}
      <ToastContainer />

      {/* RUTA: LOGIN CLÍNICO */}
      {currentRoute === 'login' && (
        <LoginClinicPage 
          usuarios={usuarios} 
          clinicas={clinicas}
          onLogin={(usr) => handleLogin(usr, 'user')} 
          navigateToAdmin={() => setCurrentRoute('login-admin')} 
        />
      )}

      {/* RUTA: LOGIN ADMINISTRADOR */}
      {currentRoute === 'login-admin' && (
        <LoginAdminPage 
          admins={administradores}
          onLogin={(adm) => handleLogin(adm, 'admin')} 
          navigateToClinic={() => setCurrentRoute('login')} 
        />
      )}

      {/* RUTA: DASHBOARD CLÍNICO */}
      {currentRoute === 'dashboard' && currentUser?.sessionType === 'user' && (
        <DashboardClinicPage 
          currentUser={currentUser}
          clinicas={clinicas}
          onLogout={handleLogout}
          tempFilters={tempFilters}
          setTempFilters={setTempFilters}
          handleApplyFilters={handleApplyFilters}
          handleClearFilters={handleClearFilters}
          hasActiveFilters={hasActiveFilters}
          paginatedLaudos={paginatedLaudos}
          filteredLaudosCount={filteredLaudos.length}
          currentPage={currentPage}
          setCurrentPage={setCurrentPage}
          totalPages={totalPages}
          itemsPerPage={itemsPerPage}
          onViewPDF={(laudo) => {
            setRouteParams({ id_clinica: laudo.ID_CLINICA, id_laudo: laudo.id, laudo });
            setCurrentRoute('ver-pdf');
          }}
        />
      )}

      {/* RUTA: DASHBOARD ADMINISTRADOR */}
      {currentRoute === 'dashboard-admin' && currentUser?.sessionType === 'admin' && (
        <DashboardAdminPage 
          currentUser={currentUser}
          clinicas={clinicas}
          usuarios={usuarios}
          admins={administradores}
          onLogout={handleLogout}
          activeTab={activeAdminTab}
          setActiveTab={setActiveAdminTab}
          tempFilters={tempFilters}
          setTempFilters={setTempFilters}
          handleApplyFilters={handleApplyFilters}
          handleClearFilters={handleClearFilters}
          hasActiveFilters={hasActiveFilters}
          paginatedLaudos={paginatedLaudos}
          filteredLaudosCount={filteredLaudos.length}
          currentPage={currentPage}
          setCurrentPage={setCurrentPage}
          totalPages={totalPages}
          itemsPerPage={itemsPerPage}
          onViewPDF={(laudo) => {
            setRouteParams({ id_clinica: laudo.ID_CLINICA, id_laudo: laudo.id, laudo });
            setCurrentRoute('ver-pdf');
          }}
          triggerDelete={triggerDelete}
          triggerCreate={triggerCreate}
          triggerEdit={triggerEdit}
        />
      )}

      {/* RUTA: VISOR PDF FULLSCREEN */}
      {currentRoute === 'ver-pdf' && (
        <PDFViewerPage 
          params={routeParams}
          clinica={clinicas.find(c => c.id === routeParams.id_clinica)}
          onClose={() => {
            if (currentUser?.sessionType === 'admin') {
              setCurrentRoute('dashboard-admin');
            } else {
              setCurrentRoute('dashboard');
            }
          }}
        />
      )}

      {/* MODAL GLOBAL: CONFIRMAR ELIMINACIÓN */}
      {isDeleteModalOpen && deleteTarget && (
        <div 
          className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in"
          onClick={(e) => handleBackdropClick(e, setIsDeleteModalOpen)}
        >
          <div className="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden animate-scale-up">
            <div className="p-6 text-center">
              <div className="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-50 mb-4">
                <Icons.Warning className="w-8 h-8 text-red-500" />
              </div>
              <h3 className="text-xl font-bold text-slate-900">Confirmar Exclusão</h3>
              
              <p className="mt-3 text-sm text-slate-500">
                Tem certeza de que deseja excluir o {deleteTarget.type === 'laudo' ? 'laudo de' : deleteTarget.type} <strong className="text-slate-800 font-semibold">{deleteTarget.data.NOMBRES || deleteTarget.data.NOMBRE || deleteTarget.data.USUARIO}</strong>?
              </p>
              <p className="mt-2 text-xs text-red-500 bg-red-50/50 p-2.5 rounded-lg border border-red-100">
                Esta ação não pode ser desfeita. O documento e todas as informações vinculadas serão removidos permanentemente.
              </p>
            </div>
            <div className="bg-slate-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
              <button
                type="button"
                className="w-full sm:w-auto px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-100 font-medium text-sm transition-all active:scale-[0.98]"
                onClick={() => setIsDeleteModalOpen(false)}
              >
                Cancelar
              </button>
              <button
                type="button"
                className="w-full sm:w-auto px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 font-semibold text-sm shadow-md shadow-red-500/10 hover:shadow-lg transition-all active:scale-[0.98]"
                onClick={confirmDelete}
              >
                Sim, Excluir
              </button>
            </div>
          </div>
        </div>
      )}

      {/* MODAL GLOBAL: FORMULARIO CRUD (CREAR/EDITAR) */}
      {isCRUDModalOpen && (
        <CRUDModal 
          action={crudAction} 
          clinicas={clinicas} 
          onClose={() => setIsCRUDModalOpen(false)} 
          onSave={handleCRUDSave}
          handleBackdropClick={handleBackdropClick}
          setIsCRUDModalOpen={setIsCRUDModalOpen}
        />
      )}
    </div>
  );
}

// ==========================================
// PÁGINA: LOGIN CLINIC (/)
// ==========================================

function LoginClinicPage({ usuarios, clinicas, onLogin, navigateToAdmin }) {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    setError('');

    if (!username || !password) {
      setError('Por favor, preencha todos os campos.');
      return;
    }

    // Buscar usuario clínico
    const foundUser = usuarios.find(u => u.USUARIO.toLowerCase() === username.toLowerCase() && u.PASSWORD === password);
    
    if (foundUser) {
      // Agregar el nombre de la clínica
      const clinic = clinicas.find(c => c.id === foundUser.ID_CLINICA);
      onLogin({
        ...foundUser,
        nombre_clinica: clinic ? clinic.NOMBRE : 'Clínica Vinculada'
      });
    } else {
      setError('Credenciais inválidas. Verifique seu usuário e senha.');
    }
  };

  return (
    <div className="relative min-h-screen bg-gradient-to-br from-slate-900 to-slate-850 flex items-center justify-center overflow-hidden p-4 sm:p-6 lg:p-8">
      {/* Red neuronal animada en Canvas */}
      <NeuralBackground />

      {/* Tarjeta de Login Principal con diseño responsivo de doble columna */}
      <div className="relative z-10 w-full max-w-5xl bg-white/95 rounded-3xl shadow-2xl overflow-hidden border border-white/20 grid grid-cols-1 md:grid-cols-2 animate-fade-in-up">
        
        {/* Columna Izquierda (Branding, visible solo en tablets/escritorios) */}
        <div className="hidden md:flex flex-col justify-between p-12 bg-slate-50 relative overflow-hidden border-r border-slate-100">
          
          {/* Patrón de puntos decorativo en SVG */}
          <div className="absolute inset-0 opacity-[0.04] pointer-events-none" style={{ backgroundImage: 'radial-gradient(#1a9fc9 1.5px, transparent 1.5px)', backgroundSize: '24px 24px' }} />
          
          <div className="relative z-10">
            {/* Logo Simulado */}
            <div className="flex items-center gap-3">
              <div className="h-12 w-12 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white shadow-lg shadow-cyan-500/30">
                <Icons.Brain className="w-7 h-7" />
              </div>
              <div className="leading-tight">
                <span className="text-xl font-extrabold tracking-tight text-slate-800">FG NEUROLOGIA</span>
                <span className="block text-xs font-bold tracking-widest text-cyan-500">BRASIL</span>
              </div>
            </div>
          </div>

          {/* Ilustración de cerebro sutil flotando */}
          <div className="relative my-auto py-10 flex justify-center items-center">
            <div className="absolute animate-pulse opacity-40 blur-3xl w-64 h-64 bg-cyan-200 rounded-full" />
            <div className="relative animate-float">
              <Icons.Brain className="w-48 h-48 text-cyan-600/10" />
            </div>
          </div>

          <div className="relative z-10">
            <h1 className="text-2xl font-extrabold text-slate-800 leading-tight">
              Sistema de Laudos Neurológicos
            </h1>
            <p className="mt-3 text-sm text-slate-500 leading-relaxed">
              Plataforma integrada para gestão profissional de laudos médicos com tecnologia de ponta e segurança de dados.
            </p>
          </div>
        </div>

        {/* Columna Derecha (Formulario) */}
        <div className="p-8 sm:p-12 lg:p-14 flex flex-col justify-center bg-white">
          <div className="md:hidden flex justify-center mb-8">
            {/* Logo visible para móviles */}
            <div className="flex items-center gap-2.5">
              <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                <Icons.Brain className="w-6 h-6" />
              </div>
              <div className="text-left">
                <span className="text-lg font-extrabold tracking-tight text-slate-800">FG NEURO</span>
                <span className="block text-[10px] font-bold tracking-widest text-cyan-500">BRASIL</span>
              </div>
            </div>
          </div>

          <div className="text-center md:text-left mb-8">
            <h2 className="text-2xl font-extrabold text-slate-900 tracking-tight">Bem-vindo de volta</h2>
            <p className="text-sm text-slate-500 mt-1.5">Entre com suas credenciais para acessar o sistema</p>
          </div>

          {error && (
            <div className="mb-6 p-4 rounded-2xl bg-red-50 border border-red-150 text-red-700 flex gap-3 text-sm animate-shake">
              <Icons.Warning className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
              <div className="font-medium">{error}</div>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Usuário</label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <Icons.User className="w-5 h-5" />
                </div>
                <input
                  type="text"
                  required
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  className="block w-full pl-11 pr-4 py-3 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 focus:ring-0 rounded-xl text-sm font-medium transition-all outline-none"
                  placeholder="Nome de usuário clínico"
                />
              </div>
            </div>

            <div>
              <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Senha</label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <Icons.Lock className="w-5 h-5" />
                </div>
                <input
                  type="password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="block w-full pl-11 pr-4 py-3 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 focus:ring-0 rounded-xl text-sm font-medium transition-all outline-none"
                  placeholder="••••••••••••"
                />
              </div>
            </div>

            <button
              type="submit"
              className="relative group w-full py-3.5 px-4 bg-gradient-to-r from-[#1a9fc9] to-[#0d7fa3] text-white rounded-xl text-sm font-bold shadow-lg shadow-cyan-500/20 hover:shadow-xl hover:shadow-cyan-500/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] outline-none overflow-hidden"
            >
              <span className="relative z-10">Entrar no Sistema</span>
              {/* Shimmer effect en Hover */}
              <div className="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer" />
            </button>
          </form>

          {/* Enlace para Administrador */}
          <div className="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center">
            <span className="text-xs text-slate-400 font-medium mb-3">Necessita de privilégios de gestor?</span>
            <button
              onClick={navigateToAdmin}
              className="flex items-center gap-2 px-5 py-2.5 border-2 border-purple-200 hover:border-purple-500 text-purple-600 hover:bg-purple-50 rounded-xl text-xs font-bold transition-all transform hover:-translate-y-0.5 active:translate-y-0"
            >
              <Icons.Layers className="w-4 h-4 text-purple-500" />
              Entrar como Administrador
            </button>
          </div>

        </div>

      </div>
    </div>
  );
}

// ==========================================
// PÁGINA: LOGIN ADMIN (/administrar)
// ==========================================

function LoginAdminPage({ admins, onLogin, navigateToClinic }) {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = (e) => {
    e.preventDefault();
    setError('');

    if (!username || !password) {
      setError('Por favor, preencha todos os campos.');
      return;
    }

    const foundAdmin = admins.find(a => a.USUARIO.toLowerCase() === username.toLowerCase() && a.PASSWORD === password);
    
    if (foundAdmin) {
      onLogin(foundAdmin);
    } else {
      setError('Credenciais administrativas inválidas.');
    }
  };

  return (
    <div className="relative min-h-screen bg-gradient-to-br from-purple-950 to-slate-900 flex items-center justify-center overflow-hidden p-4 sm:p-6 lg:p-8">
      {/* Red neuronal animada */}
      <NeuralBackground />

      <div className="relative z-10 w-full max-w-5xl bg-white/95 rounded-3xl shadow-2xl overflow-hidden border border-white/20 grid grid-cols-1 md:grid-cols-2 animate-fade-in-up">
        
        {/* Columna Izquierda (Branding) */}
        <div className="hidden md:flex flex-col justify-between p-12 bg-slate-50 relative overflow-hidden border-r border-slate-100">
          <div className="absolute inset-0 opacity-[0.04] pointer-events-none" style={{ backgroundImage: 'radial-gradient(#9333ea 1.5px, transparent 1.5px)', backgroundSize: '24px 24px' }} />
          
          <div className="relative z-10">
            <div className="flex items-center gap-3">
              <div className="h-12 w-12 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                <Icons.Layers className="w-7 h-7" />
              </div>
              <div className="leading-tight">
                <span className="text-xl font-extrabold tracking-tight text-slate-800">FG NEUROLOGIA</span>
                <span className="block text-xs font-bold tracking-widest text-purple-500">PAINEL DE CONTROLE</span>
              </div>
            </div>
          </div>

          <div className="relative my-auto py-10 flex justify-center items-center">
            <div className="absolute animate-pulse opacity-40 blur-3xl w-64 h-64 bg-purple-200 rounded-full" />
            <div className="relative animate-float">
              <Icons.Layers className="w-48 h-48 text-purple-600/10" />
            </div>
          </div>

          <div className="relative z-10">
            <h1 className="text-2xl font-extrabold text-slate-800 leading-tight">
              Painel de Administração
            </h1>
            <p className="mt-3 text-sm text-slate-500 leading-relaxed">
              Acesso exclusivo para administradores do sistema. Gerencie todos os laudos e clínicas de forma centralizada.
            </p>
          </div>
        </div>

        {/* Columna Derecha (Formulario) */}
        <div className="p-8 sm:p-12 lg:p-14 flex flex-col justify-center bg-white">
          <div className="md:hidden flex justify-center mb-8">
            <div className="flex items-center gap-2.5">
              <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                <Icons.Layers className="w-6 h-6" />
              </div>
              <div className="text-left">
                <span className="text-lg font-extrabold tracking-tight text-slate-800">FG ADMIN</span>
                <span className="block text-[10px] font-bold tracking-widest text-purple-500">CONEXÃO</span>
              </div>
            </div>
          </div>

          <div className="text-center md:text-left mb-8">
            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md shadow-purple-500/15 mb-3.5">
              <Icons.Layers className="w-3.5 h-3.5" />
              Acesso Administrativo
            </span>
            <h2 className="text-2xl font-extrabold text-slate-900 tracking-tight">Login de Administrador</h2>
            <p className="text-sm text-slate-500 mt-1.5 font-medium">Entre com suas credenciais de administrador</p>
          </div>

          {error && (
            <div className="mb-6 p-4 rounded-2xl bg-red-50 border border-red-150 text-red-700 flex gap-3 text-sm animate-shake">
              <Icons.Warning className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
              <div className="font-medium">{error}</div>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Usuário Admin</label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <Icons.User className="w-5 h-5" />
                </div>
                <input
                  type="text"
                  required
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  className="block w-full pl-11 pr-4 py-3 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-purple-500 focus:ring-0 rounded-xl text-sm font-medium transition-all outline-none"
                  placeholder="Nome de usuário administrativo"
                />
              </div>
            </div>

            <div>
              <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Senha</label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                  <Icons.Lock className="w-5 h-5" />
                </div>
                <input
                  type="password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="block w-full pl-11 pr-4 py-3 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-purple-500 focus:ring-0 rounded-xl text-sm font-medium transition-all outline-none"
                  placeholder="••••••••••••"
                />
              </div>
            </div>

            <button
              type="submit"
              className="relative group w-full py-3.5 px-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/20 hover:shadow-xl hover:shadow-purple-500/30 transition-all transform hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] outline-none overflow-hidden"
            >
              <span className="relative z-10">Acessar Painel Admin</span>
              <div className="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:animate-shimmer" />
            </button>
          </form>

          {/* Enlace para Volver al Login Clínico */}
          <div className="mt-8 pt-6 border-t border-slate-100 flex flex-col items-center">
            <span className="text-xs text-slate-400 font-medium mb-3">Deseja acessar os laudos da sua clínica?</span>
            <button
              onClick={navigateToClinic}
              className="flex items-center gap-2 px-5 py-2.5 border-2 border-slate-200 hover:border-cyan-500 text-slate-600 hover:text-cyan-600 hover:bg-slate-50 rounded-xl text-xs font-bold transition-all transform hover:-translate-y-0.5 active:translate-y-0"
            >
              <Icons.Brain className="w-4 h-4 text-cyan-500" />
              Área de Clínicas Conveniadas
            </button>
          </div>

        </div>

      </div>
    </div>
  );
}

// ==========================================
// COMPONENTE: Filtros y Barra de Búsqueda
// ==========================================

function SearchFilters({ tempFilters, setTempFilters, onApply, onClear, hasActiveFilters, isAdmin, clinicas }) {
  return (
    <div className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
      <div className="grid grid-cols-1 md:grid-cols-12 gap-3.5">
        
        {/* Barra de Búsqueda Principal */}
        <div className={`col-span-1 ${isAdmin ? 'md:col-span-4' : 'md:col-span-6'}`}>
          <label className="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pesquisa Avançada</label>
          <div className="relative">
            <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <Icons.Search className="w-5 h-5" />
            </div>
            <input
              type="text"
              value={tempFilters.search}
              onChange={(e) => setTempFilters({ ...tempFilters, search: e.target.value })}
              className="block w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
              placeholder="Nome, documento, código do laudo..."
            />
          </div>
        </div>

        {/* Dropdown de Clínicas (Solo visible para Administradores) */}
        {isAdmin && (
          <div className="col-span-1 md:col-span-3">
            <label className="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Clínica Conveniada</label>
            <div className="relative">
              <select
                value={tempFilters.clinicaId}
                onChange={(e) => setTempFilters({ ...tempFilters, clinicaId: e.target.value })}
                className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none appearance-none"
              >
                <option value="ALL">Todas as Clínicas</option>
                {clinicas.map(c => (
                  <option key={c.id} value={c.id}>{c.NOMBRE}</option>
                ))}
              </select>
              <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400">
                <svg className="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
              </div>
            </div>
          </div>
        )}

        {/* Picker Fecha Inicio */}
        <div className="col-span-1 md:col-span-2.5 flex-1">
          <label className="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data Início</label>
          <div className="relative">
            <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <Icons.Calendar className="w-5 h-5" />
            </div>
            <input
              type="date"
              value={tempFilters.fechaInicio}
              onChange={(e) => setTempFilters({ ...tempFilters, fechaInicio: e.target.value })}
              className="block w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
            />
          </div>
        </div>

        {/* Picker Fecha Fin */}
        <div className="col-span-1 md:col-span-2.5 flex-1">
          <label className="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Data Fim</label>
          <div className="relative">
            <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
              <Icons.Calendar className="w-5 h-5" />
            </div>
            <input
              type="date"
              value={tempFilters.fechaFin}
              onChange={(e) => setTempFilters({ ...tempFilters, fechaFin: e.target.value })}
              className="block w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
            />
          </div>
        </div>

      </div>

      {/* Botones de acción de Filtros */}
      <div className="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
        {hasActiveFilters && (
          <button
            onClick={onClear}
            className="flex items-center gap-1.5 px-4 py-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 text-xs font-bold rounded-xl transition-all"
          >
            <Icons.XMark className="w-4 h-4" />
            Limpar Filtros
          </button>
        )}
        <button
          onClick={onApply}
          className="flex items-center gap-1.5 px-5 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-cyan-500/10 hover:shadow-lg transition-all transform active:scale-95"
        >
          <Icons.Funnel className="w-4 h-4" />
          Filtrar Busca
        </button>
      </div>
    </div>
  );
}

// ==========================================
// COMPONENTE: Paginación Standard
// ==========================================

function Pagination({ currentPage, totalPages, setCurrentPage, itemsTotalCount, itemsPerPage }) {
  const getPageNumbers = () => {
    const pageNumbers = [];
    const maxVisible = 5;

    if (totalPages <= maxVisible) {
      for (let i = 1; i <= totalPages; i++) pageNumbers.push(i);
    } else {
      let start = Math.max(1, currentPage - 2);
      let end = Math.min(totalPages, currentPage + 2);

      if (start === 1) {
        end = 5;
      } else if (end === totalPages) {
        start = totalPages - 4;
      }

      for (let i = start; i <= end; i++) pageNumbers.push(i);
    }
    return pageNumbers;
  };

  const showingStart = itemsTotalCount === 0 ? 0 : (currentPage - 1) * itemsPerPage + 1;
  const showingEnd = Math.min(currentPage * itemsPerPage, itemsTotalCount);

  return (
    <div className="bg-white border-t border-slate-200 px-5 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
      {/* Texto Informativo */}
      <div className="text-xs font-semibold text-slate-500">
        Mostrando <span className="text-slate-800">{showingStart}</span> a <span className="text-slate-800">{showingEnd}</span> de <span className="text-slate-800">{itemsTotalCount}</span> resultados
      </div>

      {/* Botones de Navegación */}
      <div className="flex items-center gap-1.5">
        <button
          onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
          disabled={currentPage === 1}
          className="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 disabled:opacity-40 disabled:hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition-all flex items-center gap-1"
        >
          <Icons.ChevronLeft className="w-4 h-4" />
          Anterior
        </button>

        {getPageNumbers().map(num => (
          <button
            key={num}
            onClick={() => setCurrentPage(num)}
            className={`h-9 w-9 flex items-center justify-center rounded-xl text-xs font-bold transition-all ${
              currentPage === num
                ? 'bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-md shadow-cyan-500/15'
                : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'
            }`}
          >
            {num}
          </button>
        ))}

        <button
          onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
          disabled={currentPage === totalPages}
          className="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 disabled:opacity-40 disabled:hover:bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition-all flex items-center gap-1"
        >
          Próximo
          <Icons.ChevronRight className="w-4 h-4" />
        </button>
      </div>
    </div>
  );
}

// ==========================================
// COMPONENTE: LaudoMobileCard
// ==========================================

function LaudoMobileCard({ laudo, clinicName, onViewPDF, onDownload, onDelete, isAdmin }) {
  const patientInitials = laudo.NOMBRES ? laudo.NOMBRES.charAt(0) : 'P';
  
  // Función para formatear fechas a formato brasileño dd/mm/yyyy
  const formatBrazilianDate = (dateStr) => {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
      return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return dateStr;
  };

  return (
    <div className="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm space-y-4 animate-fade-in">
      <div className="flex items-center gap-3">
        {/* Avatar */}
        <div className="h-11 w-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-extrabold text-base shadow-sm">
          {patientInitials}
        </div>
        
        <div className="flex-1 min-w-0">
          <h4 className="text-sm font-bold text-slate-900 truncate">{laudo.NOMBRES}</h4>
          <span className="text-[11px] text-slate-500 font-medium block">Exame: {formatBrazilianDate(laudo.FECHA_ESTUDIO)}</span>
          <span className="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">{laudo.id_documento} | CPF: {laudo.DOCUMENTO}</span>
        </div>
      </div>

      <div className="flex flex-wrap gap-2 pt-2 border-t border-slate-50">
        <span className="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-100 uppercase tracking-wide">
          {laudo.TIPO_ESTUDIO}
        </span>
        {isAdmin && clinicName && (
          <span className="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100 uppercase tracking-wide">
            {clinicName}
          </span>
        )}
      </div>

      {/* Botones de acción responsivos full-width */}
      <div className="flex gap-2 pt-1.5">
        <button
          onClick={() => onViewPDF(laudo)}
          className="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all"
        >
          <Icons.Eye className="w-4 h-4 text-slate-500" />
          Ver
        </button>
        <button
          onClick={() => onDownload(laudo)}
          className="flex-1 py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-bold flex items-center justify-center gap-1.5 transition-all"
        >
          <Icons.Download className="w-4 h-4 text-emerald-600" />
          Baixar
        </button>
        {isAdmin && onDelete && (
          <button
            onClick={() => onDelete('laudo', laudo)}
            className="p-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-100 rounded-xl transition-all"
          >
            <Icons.Trash className="w-4.5 h-4.5" />
          </button>
        )}
      </div>
    </div>
  );
}

// ==========================================
// PÁGINA: DASHBOARD CLINIC (/dashboard)
// ==========================================

function DashboardClinicPage({
  currentUser,
  clinicas,
  onLogout,
  tempFilters,
  setTempFilters,
  handleApplyFilters,
  handleClearFilters,
  hasActiveFilters,
  paginatedLaudos,
  filteredLaudosCount,
  currentPage,
  setCurrentPage,
  totalPages,
  itemsPerPage,
  onViewPDF
}) {

  // Función para forzar descarga del PDF ficticio
  const handleDownloadPDF = (laudo) => {
    toast.info(`Iniciando download do laudo: ${laudo.id_documento}.pdf`);
    setTimeout(() => {
      // Simular descarga mediante la creación de un elemento ancla temporal
      const link = document.createElement('a');
      link.href = '#';
      link.setAttribute('download', `${laudo.id_documento}.pdf`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      toast.success('Download concluído com sucesso!');
    }, 1500);
  };

  // Función para formatear fechas a formato brasileño dd/mm/yyyy
  const formatBrazilianDate = (dateStr) => {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
      return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return dateStr;
  };

  return (
    <div className="min-h-screen flex flex-col bg-sky-50/20">
      
      {/* HEADER STICKY */}
      <header className="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80 shadow-sm transition-all duration-300">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          
          {/* Lado izquierdo: Logo e Identidad */}
          <div className="flex items-center gap-3">
            <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white shadow-md">
              <Icons.Brain className="w-6 h-6" />
            </div>
            <div className="h-6 w-px bg-slate-200 hidden sm:block" />
            <div className="leading-tight hidden sm:block">
              <span className="text-sm font-extrabold tracking-tight text-slate-800">FG NEUROLOGIA</span>
              <span className="block text-[9px] font-bold tracking-widest text-cyan-500 uppercase">BRASIL</span>
            </div>
          </div>

          {/* Lado derecho: Sesión y Control */}
          <div className="flex items-center gap-4">
            <div className="text-right hidden md:block">
              <span className="text-xs text-slate-400 font-semibold block">Acesso Conveniado</span>
              <span className="text-sm font-bold text-slate-800">
                {currentUser.NOMBRES} {currentUser.APELLIDOS}
              </span>
            </div>

            {/* Badge Clínico */}
            <span className="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-cyan-500 to-blue-600 text-white shadow-sm">
              <Icons.Building className="w-3.5 h-3.5" />
              {currentUser.nombre_clinica}
            </span>

            <button
              onClick={onLogout}
              className="flex items-center gap-1.5 px-3.5 py-2 border border-slate-200 hover:border-red-200 hover:text-red-600 hover:bg-red-50 text-slate-600 font-bold text-xs rounded-xl transition-all"
            >
              <Icons.LogOut className="w-4 h-4" />
              Sair
            </button>
          </div>

        </div>
      </header>

      {/* CONTENIDO DEL DASHBOARD */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        {/* Encabezado del Dashboard */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div>
            <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Painel de Laudos Neurológicos</h1>
            <p className="text-sm text-slate-500 mt-1">Busque, visualize e faça o download de laudos assinados digitalmente.</p>
          </div>
        </div>

        {/* MÓDULO DE FILTROS */}
        <SearchFilters 
          tempFilters={tempFilters} 
          setTempFilters={setTempFilters} 
          onApply={handleApplyFilters} 
          onClear={handleClearFilters} 
          hasActiveFilters={hasActiveFilters}
          isAdmin={false}
          clinicas={clinicas}
        />

        {/* LISTADO DE LAUDOS (Desktop vs Mobile) */}
        <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
          
          {/* Vista Móvil (Visible solo en <=768px) */}
          <div className="block md:hidden p-4 space-y-3 bg-slate-50/50">
            {paginatedLaudos.length > 0 ? (
              paginatedLaudos.map(laudo => (
                <LaudoMobileCard
                  key={laudo.id}
                  laudo={laudo}
                  clinicName=""
                  onViewPDF={onViewPDF}
                  onDownload={handleDownloadPDF}
                  isAdmin={false}
                />
              ))
            ) : (
              <div className="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-slate-200 p-6">
                <Icons.DocumentText className="w-12 h-12 text-slate-300 mx-auto mb-3" />
                <p className="text-sm font-bold text-slate-700">Nenhum laudo encontrado</p>
                <p className="text-xs text-slate-400 mt-1">Ajuste os filtros de data ou barra de pesquisa para tentar novamente.</p>
              </div>
            )}
          </div>

          {/* Tabla de Escritorio (Visible en >768px) */}
          <div className="hidden md:block overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="bg-slate-50 border-b border-slate-200">
                  <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Paciente</th>
                  <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Documento / CPF</th>
                  <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Estudo / Exame</th>
                  <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {paginatedLaudos.length > 0 ? (
                  paginatedLaudos.map(laudo => {
                    const initials = laudo.NOMBRES ? laudo.NOMBRES.charAt(0) : 'P';
                    return (
                      <tr key={laudo.id} className="hover:bg-slate-50/80 transition-all">
                        {/* Paciente con Avatar */}
                        <td className="py-4 px-6">
                          <div className="flex items-center gap-3.5">
                            <div className="h-11 w-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                              {initials}
                            </div>
                            <div>
                              <div className="font-bold text-slate-900 text-[14px]">{laudo.NOMBRES}</div>
                              <div className="text-slate-400 text-xs mt-0.5 flex items-center gap-1.5">
                                <Icons.Calendar className="w-3.5 h-3.5 shrink-0" />
                                Data do exame: {formatBrazilianDate(laudo.FECHA_ESTUDIO)}
                              </div>
                            </div>
                          </div>
                        </td>

                        {/* Documento / Código */}
                        <td className="py-4 px-6 text-sm text-slate-600 font-medium">
                          <span className="block font-semibold text-slate-800">{laudo.DOCUMENTO}</span>
                          <span className="block text-xs text-slate-400">{laudo.id_documento}</span>
                        </td>

                        {/* Tipo de Estudio Badge */}
                        <td className="py-4 px-6">
                          <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-100 uppercase tracking-wide">
                            {laudo.TIPO_ESTUDIO}
                          </span>
                        </td>

                        {/* Acciones */}
                        <td className="py-4 px-6 text-right">
                          <div className="inline-flex gap-2">
                            <button
                              onClick={() => onViewPDF(laudo)}
                              className="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm active:scale-95"
                            >
                              <Icons.Eye className="w-4 h-4 text-slate-500" />
                              Ver
                            </button>
                            <button
                              onClick={() => handleDownloadPDF(laudo)}
                              className="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-150 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-all shadow-sm active:scale-95"
                            >
                              <Icons.Download className="w-4 h-4 text-emerald-600" />
                              Baixar
                            </button>
                          </div>
                        </td>
                      </tr>
                    );
                  })
                ) : (
                  <tr>
                    <td colSpan="4" className="text-center py-16">
                      <Icons.DocumentText className="w-14 h-14 text-slate-300 mx-auto mb-3.5" />
                      <p className="text-base font-bold text-slate-800">Nenhum laudo encontrado nesta clínica</p>
                      <p className="text-xs text-slate-400 mt-1">Experimente alterar a data ou termo de pesquisa.</p>
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {/* Módulo de Paginación */}
          <Pagination
            currentPage={currentPage}
            totalPages={totalPages}
            setCurrentPage={setCurrentPage}
            itemsTotalCount={filteredLaudosCount}
            itemsPerPage={itemsPerPage}
          />

        </div>

      </main>

    </div>
  );
}

// ==========================================
// PÁGINA: DASHBOARD ADMIN (/dashboardadmin)
// ==========================================

function DashboardAdminPage({
  currentUser,
  clinicas,
  usuarios,
  admins,
  onLogout,
  activeTab,
  setActiveTab,
  tempFilters,
  setTempFilters,
  handleApplyFilters,
  handleClearFilters,
  hasActiveFilters,
  paginatedLaudos,
  filteredLaudosCount,
  currentPage,
  setCurrentPage,
  totalPages,
  itemsPerPage,
  onViewPDF,
  triggerDelete,
  triggerCreate,
  triggerEdit
}) {

  // Función para forzar descarga del PDF ficticio
  const handleDownloadPDF = (laudo) => {
    toast.info(`Iniciando download do laudo: ${laudo.id_documento}.pdf`);
    setTimeout(() => {
      const link = document.createElement('a');
      link.href = '#';
      link.setAttribute('download', `${laudo.id_documento}.pdf`);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      toast.success('Download concluído com sucesso!');
    }, 1500);
  };

  // Función para formatear fechas a formato brasileño dd/mm/yyyy
  const formatBrazilianDate = (dateStr) => {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
      return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return dateStr;
  };

  return (
    <div className="min-h-screen flex flex-col bg-slate-50/50">
      
      {/* HEADER ADAPTADO PARA ADMINISTRACIÓN */}
      <header className="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          
          <div className="flex items-center gap-3">
            <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-purple-500/10">
              <Icons.Layers className="w-6 h-6" />
            </div>
            <div className="h-6 w-px bg-slate-200 hidden sm:block" />
            <div className="leading-tight hidden sm:block">
              <span className="text-sm font-extrabold tracking-tight text-slate-800">FG NEUROLOGIA</span>
              <span className="block text-[9px] font-bold tracking-widest text-purple-500 uppercase">ADMINISTRAÇÃO CENTRAL</span>
            </div>
          </div>

          <div className="flex items-center gap-4">
            <div className="text-right hidden md:block">
              <span className="text-xs text-slate-400 font-semibold block">Painel Geral</span>
              <span className="text-sm font-bold text-slate-800">
                {currentUser.NOMBRES} {currentUser.APELLIDOS}
              </span>
            </div>

            <span className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-md shadow-purple-500/10">
              <Icons.Layers className="w-3.5 h-3.5" />
              Admin
            </span>

            <button
              onClick={onLogout}
              className="flex items-center gap-1.5 px-3.5 py-2 border border-slate-200 hover:border-red-200 hover:text-red-600 hover:bg-red-50 text-slate-600 font-bold text-xs rounded-xl transition-all"
            >
              <Icons.LogOut className="w-4 h-4" />
              Sair
            </button>
          </div>

        </div>
      </header>

      {/* TABS DE NAVEGACIÓN PRINCIPAL */}
      <nav className="bg-white border-b border-slate-200 sticky top-16 z-35">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-1.5 overflow-x-auto">
          
          {/* Tab 1: Laudos */}
          <button
            onClick={() => setActiveTab('laudos')}
            className={`flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 ${
              activeTab === 'laudos'
                ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20'
                : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'
            }`}
          >
            <Icons.DocumentText className="w-5 h-5" />
            Laudos Neurológicos
          </button>

          {/* Tab 2: Clínicas */}
          <button
            onClick={() => setActiveTab('clinicas')}
            className={`flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 ${
              activeTab === 'clinicas'
                ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20'
                : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'
            }`}
          >
            <Icons.Building className="w-5 h-5" />
            Clínicas Conveniadas
          </button>

          {/* Tab 3: Usuarios Clínicos */}
          <button
            onClick={() => setActiveTab('usuarios')}
            className={`flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 ${
              activeTab === 'usuarios'
                ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20'
                : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'
            }`}
          >
            <Icons.Users className="w-5 h-5" />
            Usuários de Clínicas
          </button>

          {/* Tab 4: Administradores */}
          <button
            onClick={() => setActiveTab('admins')}
            className={`flex items-center gap-2 py-4 px-5 text-sm font-bold transition-all relative border-b-2 shrink-0 ${
              activeTab === 'admins'
                ? 'border-cyan-500 text-cyan-600 bg-cyan-50/20'
                : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50'
            }`}
          >
            <Icons.Layers className="w-5 h-5" />
            Administradores
          </button>

        </div>
      </nav>

      {/* VISTAS DINÁMICAS BASADAS EN TABS */}
      <main className="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {/* CONTENIDO TAB: LAUDOS */}
        {activeTab === 'laudos' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div>
                <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Gerenciamento Geral de Laudos</h1>
                <p className="text-sm text-slate-500 mt-1">Adicione, edite, exclua e visualize laudos médicos de qualquer clínica parceira.</p>
              </div>
              <button
                onClick={() => triggerCreate('laudo')}
                className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-cyan-500/20 hover:shadow-xl transition-all transform hover:-translate-y-0.5 active:translate-y-0"
              >
                <Icons.Plus className="w-5 h-5" />
                Novo Laudo Médico
              </button>
            </div>

            {/* Módulo de Filtros (Incluyendo selector de Clínica para administradores) */}
            <SearchFilters 
              tempFilters={tempFilters} 
              setTempFilters={setTempFilters} 
              onApply={handleApplyFilters} 
              onClear={handleClearFilters} 
              hasActiveFilters={hasActiveFilters}
              isAdmin={true}
              clinicas={clinicas}
            />

            {/* Listado en tarjetas móviles para laudos */}
            <div className="block md:hidden space-y-3">
              {paginatedLaudos.length > 0 ? (
                paginatedLaudos.map(laudo => {
                  const clinic = clinicas.find(c => c.id === laudo.ID_CLINICA);
                  return (
                    <LaudoMobileCard
                      key={laudo.id}
                      laudo={laudo}
                      clinicName={clinic ? clinic.NOMBRE : 'Indefinida'}
                      onViewPDF={onViewPDF}
                      onDownload={handleDownloadPDF}
                      onDelete={triggerDelete}
                      isAdmin={true}
                    />
                  );
                })
              ) : (
                <div className="text-center py-12 bg-white rounded-2xl border border-slate-200">
                  <Icons.DocumentText className="w-12 h-12 text-slate-300 mx-auto mb-3" />
                  <p className="text-sm font-bold text-slate-700">Nenhum laudo encontrado</p>
                </div>
              )}
            </div>

            {/* Tabla Escritorio */}
            <div className="hidden md:block bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-200">
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Paciente</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Documento / CPF</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Clínica Origem</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Estudo</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {paginatedLaudos.length > 0 ? (
                      paginatedLaudos.map(laudo => {
                        const initials = laudo.NOMBRES ? laudo.NOMBRES.charAt(0) : 'P';
                        const clinic = clinicas.find(c => c.id === laudo.ID_CLINICA);
                        return (
                          <tr key={laudo.id} className="hover:bg-slate-50/80 transition-all">
                            {/* Paciente */}
                            <td className="py-4 px-6">
                              <div className="flex items-center gap-3">
                                <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white font-extrabold text-sm shadow-sm">
                                  {initials}
                                </div>
                                <div>
                                  <div className="font-bold text-slate-900 text-[14px]">{laudo.NOMBRES}</div>
                                  <div className="text-slate-400 text-xs mt-0.5">
                                    Exame: {formatBrazilianDate(laudo.FECHA_ESTUDIO)}
                                  </div>
                                </div>
                              </div>
                            </td>

                            {/* Documento / Código */}
                            <td className="py-4 px-6 text-sm text-slate-600 font-medium">
                              <span className="block font-semibold text-slate-800">{laudo.DOCUMENTO}</span>
                              <span className="block text-xs text-slate-400">{laudo.id_documento}</span>
                            </td>

                            {/* Clínica con Badge Púrpura */}
                            <td className="py-4 px-6">
                              <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100 uppercase tracking-wide">
                                <Icons.Building className="w-3.5 h-3.5" />
                                {clinic ? clinic.NOMBRE : 'Inexistente'}
                              </span>
                            </td>

                            {/* Tipo Estudio */}
                            <td className="py-4 px-6">
                              <span className="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-50 text-cyan-700 border border-cyan-100 uppercase tracking-wide">
                                {laudo.TIPO_ESTUDIO}
                              </span>
                            </td>

                            {/* Acciones del Administrador */}
                            <td className="py-4 px-6 text-right">
                              <div className="inline-flex gap-1.5">
                                <button
                                  onClick={() => onViewPDF(laudo)}
                                  className="p-2 hover:bg-slate-100 text-slate-500 hover:text-slate-800 rounded-xl transition-all"
                                  title="Visualizar PDF"
                                >
                                  <Icons.Eye className="w-4.5 h-4.5" />
                                </button>
                                <button
                                  onClick={() => handleDownloadPDF(laudo)}
                                  className="p-2 hover:bg-emerald-50 text-slate-500 hover:text-emerald-700 rounded-xl transition-all"
                                  title="Baixar PDF"
                                >
                                  <Icons.Download className="w-4.5 h-4.5" />
                                </button>
                                <button
                                  onClick={() => triggerEdit('laudo', laudo)}
                                  className="p-2 hover:bg-cyan-50 text-slate-500 hover:text-cyan-700 rounded-xl transition-all"
                                  title="Editar Laudo"
                                >
                                  <Icons.Pencil className="w-4.5 h-4.5" />
                                </button>
                                <button
                                  onClick={() => triggerDelete('laudo', laudo)}
                                  className="p-2 hover:bg-red-50 text-slate-500 hover:text-red-600 rounded-xl transition-all"
                                  title="Excluir Laudo"
                                >
                                  <Icons.Trash className="w-4.5 h-4.5" />
                                </button>
                              </div>
                            </td>
                          </tr>
                        );
                      })
                    ) : (
                      <tr>
                        <td colSpan="5" className="text-center py-16">
                          <Icons.DocumentText className="w-14 h-14 text-slate-300 mx-auto mb-3.5" />
                          <p className="text-base font-bold text-slate-800">Nenhum laudo encontrado</p>
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>

              <Pagination
                currentPage={currentPage}
                totalPages={totalPages}
                setCurrentPage={setCurrentPage}
                itemsTotalCount={filteredLaudosCount}
                itemsPerPage={itemsPerPage}
              />
            </div>
          </div>
        )}

        {/* CONTENIDO TAB: CLÍNICAS */}
        {activeTab === 'clinicas' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div>
                <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Gerenciar Clínicas Conveniadas</h1>
                <p className="text-sm text-slate-500 mt-1">Gerencie os estabelecimentos de saúde habilitados a consumir o sistema de laudos.</p>
              </div>
              <button
                onClick={() => triggerCreate('clinica')}
                className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/20 hover:shadow-xl transition-all transform hover:-translate-y-0.5"
              >
                <Icons.Plus className="w-5 h-5" />
                Nova Clínica Parceira
              </button>
            </div>

            {/* Listado de Clínicas */}
            <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-200">
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome da Clínica</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Contato / Telefone</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Endereço</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {clinicas.map(c => (
                      <tr key={c.id} className="hover:bg-slate-50/50 transition-all">
                        <td className="py-4 px-6 font-bold text-slate-900">{c.NOMBRE}</td>
                        <td className="py-4 px-6 text-sm text-slate-600 font-medium">
                          <span className="block">{c.EMAIL || 'Não cadastrado'}</span>
                          <span className="block text-xs text-slate-400">{c.TELEFONO}</span>
                        </td>
                        <td className="py-4 px-6 text-xs text-slate-500 font-medium max-w-xs truncate">{c.DIRECCION}</td>
                        <td className="py-4 px-6 text-right">
                          <div className="inline-flex gap-2">
                            <button
                              onClick={() => triggerEdit('clinica', c)}
                              className="px-3.5 py-2 bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 border border-slate-200 hover:border-cyan-200 rounded-xl text-xs font-bold transition-all"
                            >
                              Editar
                            </button>
                            <button
                              onClick={() => triggerDelete('clinica', c)}
                              className="px-3.5 py-2 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 border border-slate-200 hover:border-red-200 rounded-xl text-xs font-bold transition-all"
                            >
                              Excluir
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* CONTENIDO TAB: USUARIOS DE CLÍNICA */}
        {activeTab === 'usuarios' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div>
                <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Gerenciar Usuários de Clínicas</h1>
                <p className="text-sm text-slate-500 mt-1">Controle de acesso para os profissionais clínicos visualizarem os PDFs de cada clínica parceira.</p>
              </div>
              <button
                onClick={() => triggerCreate('usuario')}
                className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/20 hover:shadow-xl transition-all transform hover:-translate-y-0.5"
              >
                <Icons.Plus className="w-5 h-5" />
                Novo Usuário Clínico
              </button>
            </div>

            {/* Listado de Usuarios */}
            <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-200">
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome Completo</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome de Usuário (Login)</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Clínica Vinculada</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {usuarios.map(u => {
                      const clinicaVinculada = clinicas.find(c => c.id === u.ID_CLINICA);
                      return (
                        <tr key={u.id} className="hover:bg-slate-50/50 transition-all">
                          <td className="py-4 px-6 font-bold text-slate-900">
                            {u.NOMBRES} {u.APELLIDOS}
                          </td>
                          <td className="py-4 px-6 text-sm text-slate-600 font-semibold">{u.USUARIO}</td>
                          <td className="py-4 px-6">
                            <span className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100 uppercase tracking-wide">
                              <Icons.Building className="w-3.5 h-3.5" />
                              {clinicaVinculada ? clinicaVinculada.NOMBRE : 'Nenhuma'}
                            </span>
                          </td>
                          <td className="py-4 px-6 text-right">
                            <div className="inline-flex gap-2">
                              <button
                                onClick={() => triggerEdit('usuario', u)}
                                className="px-3.5 py-2 bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 border border-slate-200 hover:border-cyan-200 rounded-xl text-xs font-bold transition-all"
                              >
                                Editar
                              </button>
                              <button
                                onClick={() => triggerDelete('usuario', u)}
                                className="px-3.5 py-2 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 border border-slate-200 hover:border-red-200 rounded-xl text-xs font-bold transition-all"
                              >
                                Excluir
                              </button>
                            </div>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* CONTENIDO TAB: ADMINISTRADORES */}
        {activeTab === 'admins' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div>
                <h1 className="text-2xl font-extrabold text-slate-900 tracking-tight">Gerenciar Administradores</h1>
                <p className="text-sm text-slate-500 mt-1">Crie e edite as contas que possuem poderes absolutos sobre laudos, clínicas e usuários.</p>
              </div>
              <button
                onClick={() => triggerCreate('admin')}
                className="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/20 hover:shadow-xl transition-all transform hover:-translate-y-0.5"
              >
                <Icons.Plus className="w-5 h-5" />
                Novo Administrador
              </button>
            </div>

            {/* Listado de Admins */}
            <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-slate-50 border-b border-slate-200">
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome do Administrador</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Nome de Usuário (Login)</th>
                      <th className="py-4 px-6 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest text-right">Ações</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-100">
                    {admins.map(a => (
                      <tr key={a.id} className="hover:bg-slate-50/50 transition-all">
                        <td className="py-4 px-6 font-bold text-slate-900">
                          {a.NOMBRES} {a.APELLIDOS}
                        </td>
                        <td className="py-4 px-6 text-sm text-slate-600 font-semibold">{a.USUARIO}</td>
                        <td className="py-4 px-6 text-right">
                          <div className="inline-flex gap-2">
                            <button
                              onClick={() => triggerEdit('admin', a)}
                              className="px-3.5 py-2 bg-slate-50 hover:bg-cyan-50 text-slate-600 hover:text-cyan-700 border border-slate-200 hover:border-cyan-200 rounded-xl text-xs font-bold transition-all"
                            >
                              Editar
                            </button>
                            <button
                              onClick={() => triggerDelete('admin', a)}
                              className="px-3.5 py-2 bg-slate-50 hover:bg-red-50 text-slate-600 hover:text-red-700 border border-slate-200 hover:border-red-200 rounded-xl text-xs font-bold transition-all"
                            >
                              Excluir
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

      </main>

    </div>
  );
}

// ==========================================
// COMPONENTE: VISUALIZADOR DE PDF (/ver-pdf)
// ==========================================

function PDFViewerPage({ params, clinica, onClose }) {
  const { laudo } = params;

  // Función para formatear fechas a formato brasileño
  const formatBrazilianDate = (dateStr) => {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
      return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return dateStr;
  };

  return (
    <div className="fixed inset-0 z-50 bg-slate-900 flex flex-col animate-fade-in">
      
      {/* Header del Visor */}
      <div className="bg-slate-950 text-white h-16 px-4 flex items-center justify-between border-b border-slate-800">
        <div className="flex items-center gap-3">
          <button
            onClick={onClose}
            className="p-2 hover:bg-slate-800 rounded-xl text-slate-400 hover:text-white transition-all flex items-center justify-center"
            title="Voltar ao Painel"
          >
            <Icons.ChevronLeft className="w-6 h-6" />
          </button>
          <div>
            <h1 className="text-sm font-bold truncate max-w-xs sm:max-w-md">{laudo?.NOMBRES}</h1>
            <span className="text-[11px] text-slate-400 font-semibold block uppercase tracking-wider">
              {laudo?.id_documento} | CPF: {laudo?.DOCUMENTO}
            </span>
          </div>
        </div>

        <div className="flex items-center gap-2">
          {/* Badge del tipo de estudio */}
          <span className="hidden sm:inline-flex px-3 py-1 bg-cyan-950 text-cyan-400 border border-cyan-900 rounded-full text-xs font-bold uppercase tracking-wider">
            {laudo?.TIPO_ESTUDIO}
          </span>
          <button
            onClick={() => {
              toast.success('Iniciando download da via física do laudo...');
            }}
            className="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition-all active:scale-[0.98]"
          >
            <Icons.Download className="w-4.5 h-4.5" />
            <span className="hidden sm:inline">Baixar PDF</span>
          </button>
        </div>
      </div>

      {/* Contenedor del PDF (Simulamos un visualizador de PDF con UI de alta fidelidad médica) */}
      <div className="flex-1 overflow-y-auto bg-slate-800 p-4 sm:p-8 flex justify-center">
        
        {/* Hoja de Laudo Físico (Simulando el papel A4 oficial de la clínica) */}
        <div className="bg-white w-full max-w-4xl shadow-2xl rounded-sm p-8 sm:p-14 text-slate-900 flex flex-col justify-between min-h-[1050px] relative border-t-8 border-cyan-500 font-serif">
          
          {/* Marca de agua de seguridad en background */}
          <div className="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.02]">
            <Icons.Brain className="w-[500px] h-[500px] text-cyan-900" />
          </div>

          <div className="space-y-8 relative z-10">
            {/* Cabecera del Documento Clínico */}
            <div className="flex items-start justify-between border-b-2 border-slate-200 pb-6">
              <div className="flex items-center gap-3">
                <div className="h-14 w-14 rounded-2xl bg-gradient-to-br from-[#1a9fc9] to-[#0d7fa3] flex items-center justify-center text-white">
                  <Icons.Brain className="w-8 h-8" />
                </div>
                <div className="text-left font-sans">
                  <span className="text-xl font-black tracking-tight text-slate-800">FG NEUROLOGIA</span>
                  <span className="block text-[10px] font-bold tracking-widest text-[#1a9fc9]">BRASIL</span>
                </div>
              </div>
              
              <div className="text-right font-sans text-xs text-slate-500 space-y-0.5">
                <p className="font-bold text-slate-800">{clinica?.NOMBRE || 'FG NEURO BRASIL'}</p>
                <p>{clinica?.DIRECCION || 'Serviços Médicos Compartilhados'}</p>
                <p>Tel: {clinica?.TELEFONO || '0800 550 1234'}</p>
              </div>
            </div>

            {/* Datos del Paciente en Grilla */}
            <div className="bg-slate-50 rounded-xl p-5 border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4 font-sans text-sm">
              <div>
                <span className="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">Paciente</span>
                <span className="font-extrabold text-slate-800 text-base">{laudo?.NOMBRES}</span>
              </div>
              <div>
                <span className="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">CPF / Documento</span>
                <span className="font-bold text-slate-700">{laudo?.DOCUMENTO}</span>
              </div>
              <div>
                <span className="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">Procedimento Realizado</span>
                <span className="font-extrabold text-cyan-600 uppercase tracking-wide">{laudo?.TIPO_ESTUDIO}</span>
              </div>
              <div>
                <span className="block text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">Data do Laudo</span>
                <span className="font-bold text-slate-700">{formatBrazilianDate(laudo?.FECHA_ESTUDIO)}</span>
              </div>
            </div>

            {/* Diagnóstico Neurológico de Simulación Médica */}
            <div className="space-y-6 pt-4">
              <h2 className="text-xl font-bold text-slate-900 border-b border-slate-200 pb-2">CONTEÚDO DO LAUDO MÉDICO</h2>
              
              <div className="space-y-4 text-justify leading-relaxed text-slate-700">
                <p>
                  <strong>1. Registro de Atividade Elétrica Basal:</strong> Ritmo alfa posterior simétrico, síncrono e reativo à abertura e ao fechamento ocular, com frequência de 9.5 Hz e amplitude média de 45 microvolts. Ritmo beta difuso de baixa amplitude, predominando nas regiões anteriores. Ritmo teta fisiológico observado em transições para sonolência.
                </p>
                <p>
                  <strong>2. Provas de Ativação Neurológica:</strong> A hiperpneia voluntária por três minutos não induziu o aparecimento de atividades lentas anormais ou paroxismos de qualquer natureza. A estimulação luminosa intermitente (fotoestimulação) nas frequências de 2 a 30 Hz não desencadeou resposta fotoparoxística.
                </p>
                <p>
                  <strong>3. Conclusão Diagnóstica:</strong> O mapeamento e registro eletroencefalográfico digital no presente exame mostram-se dentro dos limites fisiológicos da normalidade para a idade do paciente. Não há evidência de atividade epileptogênica focal ou generalizada.
                </p>
              </div>
            </div>

          </div>

          {/* Pie de Página con Firma Digitalizada */}
          <div className="border-t-2 border-slate-200 pt-8 mt-12 flex flex-col sm:flex-row justify-between items-center gap-6 font-sans">
            <div className="text-center sm:text-left text-xs text-slate-400">
              <p>Código de autenticidade digital: <strong>{laudo?.id_documento}</strong></p>
              <p className="mt-0.5">Assinado digitalmente em conformidade com as diretrizes da CFM e ICP-Brasil.</p>
            </div>
            
            <div className="text-center">
              <div className="inline-block border-b border-slate-400 pb-1.5 px-6">
                {/* Firma Digital Simulado */}
                <span className="font-cursive text-cyan-600 text-lg block select-none">Dr. Francisco Gomez Jr.</span>
              </div>
              <p className="text-slate-500 font-bold text-[10px] uppercase tracking-widest mt-1.5">Médico Neurologista Responsável</p>
              <p className="text-slate-400 text-[9px]">CRM-SP 124.908 / RQE 902</p>
            </div>
          </div>

        </div>

      </div>

    </div>
  );
}

// ==========================================
// COMPONENTE: CRUD Modal (Crear / Editar)
// ==========================================

function CRUDModal({ action, clinicas, onClose, onSave, handleBackdropClick, setIsCRUDModalOpen }) {
  const { type, entity, data } = action;

  // Estados del Formulario Basados en la Entidad
  const [nombre, setNombre] = useState(data?.NOMBRE || '');
  const [direccion, setDireccion] = useState(data?.DIRECCION || '');
  const [telefono, setTelefono] = useState(data?.TELEFONO || '');
  const [email, setEmail] = useState(data?.EMAIL || '');

  const [usuario, setUsuario] = useState(data?.USUARIO || '');
  const [password, setPassword] = useState('');
  const [nombres, setNombres] = useState(data?.NOMBRES || '');
  const [apellidos, setApellidos] = useState(data?.APELLIDOS || '');
  const [idClinica, setIdClinica] = useState(data?.ID_CLINICA || (clinicas[0]?.id || ''));

  const [documento, setDocumento] = useState(data?.DOCUMENTO || '');
  const [fechaEstudio, setFechaEstudio] = useState(data?.FECHA_ESTUDIO || '');
  const [tipoEstudio, setTipoEstudio] = useState(data?.TIPO_ESTUDIO || 'Eletroencefalograma (EEG)');

  const handleSubmit = (e) => {
    e.preventDefault();

    // Validar Campos dependientes de la entidad
    if (entity === 'clinica') {
      if (!nombre) {
        toast.error('Preencha o nome da clínica.');
        return;
      }
      onSave({ NOMBRE: nombre, DIRECCION: direccion, TELEFONO: telefono, EMAIL: email });
    }

    else if (entity === 'usuario') {
      if (!usuario || !nombres || !apellidos || !idClinica) {
        toast.error('Preencha todos os campos obrigatórios.');
        return;
      }
      if (type === 'create' && !password) {
        toast.error('Uma senha temporária é obrigatória na criação.');
        return;
      }
      onSave({ USUARIO: usuario, PASSWORD: password, NOMBRES: nombres, APELLIDOS: apellidos, ID_CLINICA: idClinica });
    }

    else if (entity === 'admin') {
      if (!usuario || !nombres || !apellidos) {
        toast.error('Preencha todos os campos obrigatórios.');
        return;
      }
      if (type === 'create' && !password) {
        toast.error('Senha obrigatória na criação.');
        return;
      }
      onSave({ USUARIO: usuario, PASSWORD: password, NOMBRES: nombres, APELLIDOS: apellidos });
    }

    else if (entity === 'laudo') {
      if (!documento || !nombres || !fechaEstudio || !tipoEstudio || !idClinica) {
        toast.error('Preencha os dados do laudo.');
        return;
      }
      onSave({ DOCUMENTO: documento, NOMBRES: nombres, FECHA_ESTUDIO: fechaEstudio, TIPO_ESTUDIO: tipoEstudio, ID_CLINICA: idClinica });
    }
  };

  const getTitle = () => {
    const isEdit = type === 'edit';
    const translationMap = {
      laudo: 'Laudo Médico',
      clinica: 'Clínica Conveniada',
      usuario: 'Usuário Clínico',
      admin: 'Administrador'
    };
    return `${isEdit ? 'Editar' : 'Novo'} ${translationMap[entity] || entity}`;
  };

  return (
    <div 
      className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm animate-fade-in"
      onClick={(e) => handleBackdropClick(e, setIsCRUDModalOpen)}
    >
      <div className="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden animate-scale-up">
        
        {/* Cabecera del Modal */}
        <div className="bg-slate-50 px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
          <h3 className="text-base font-extrabold text-slate-800 tracking-tight uppercase tracking-wider">{getTitle()}</h3>
          <button
            onClick={onClose}
            className="p-1 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-all"
          >
            <Icons.XMark className="w-5 h-5" />
          </button>
        </div>

        {/* Formulario */}
        <form onSubmit={handleSubmit}>
          <div className="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            
            {/* CAMPOS: CLÍNICA */}
            {entity === 'clinica' && (
              <>
                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nome da Clínica *</label>
                  <input
                    type="text"
                    required
                    value={nombre}
                    onChange={(e) => setNombre(e.target.value)}
                    className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                    placeholder="Ex: Clínica Neuro-Vida"
                  />
                </div>
                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Endereço Completo</label>
                  <input
                    type="text"
                    value={direccion}
                    onChange={(e) => setDireccion(e.target.value)}
                    className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                    placeholder="Av, Rua, Número, Bairro, Cidade..."
                  />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Telefone</label>
                    <input
                      type="text"
                      value={telefono}
                      onChange={(e) => setTelefono(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="(11) 99999-9999"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">E-mail</label>
                    <input
                      type="email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="contato@clinica.com"
                    />
                  </div>
                </div>
              </>
            )}

            {/* CAMPOS: USUARIO CLÍNICO */}
            {entity === 'usuario' && (
              <>
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nomes *</label>
                    <input
                      type="text"
                      required
                      value={nombres}
                      onChange={(e) => setNombres(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="Nome do profissional"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Sobrenomes *</label>
                    <input
                      type="text"
                      required
                      value={apellidos}
                      onChange={(e) => setApellidos(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="Sobrenome do profissional"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nome de Usuário (Login) *</label>
                  <input
                    type="text"
                    required
                    readOnly={type === 'edit'}
                    value={usuario}
                    onChange={(e) => setUsuario(e.target.value)}
                    className="block w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none disabled:opacity-60"
                    placeholder="Ex: dralana"
                  />
                </div>

                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">
                    Senha {type === 'create' ? '*' : '(Deixe em branco para manter)'}
                  </label>
                  <input
                    type="password"
                    required={type === 'create'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                    placeholder="••••••••••••"
                  />
                </div>

                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Clínica Vinculada *</label>
                  <div className="relative">
                    <select
                      value={idClinica}
                      onChange={(e) => setIdClinica(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none appearance-none"
                    >
                      {clinicas.map(c => (
                        <option key={c.id} value={c.id}>{c.NOMBRE}</option>
                      ))}
                    </select>
                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-405">
                      <svg className="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                  </div>
                </div>
              </>
            )}

            {/* CAMPOS: ADMINISTRADOR */}
            {entity === 'admin' && (
              <>
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nomes *</label>
                    <input
                      type="text"
                      required
                      value={nombres}
                      onChange={(e) => setNombres(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="Ex: Francisco"
                    />
                  </div>
                  <div>
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Sobrenomes *</label>
                    <input
                      type="text"
                      required
                      value={apellidos}
                      onChange={(e) => setApellidos(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="Ex: Gomez"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Nome de Usuário (Login) *</label>
                  <input
                    type="text"
                    required
                    readOnly={type === 'edit'}
                    value={usuario}
                    onChange={(e) => setUsuario(e.target.value)}
                    className="block w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none disabled:opacity-60"
                    placeholder="Ex: admin_fg"
                  />
                </div>

                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">
                    Senha {type === 'create' ? '*' : '(Deixe em branco para manter)'}
                  </label>
                  <input
                    type="password"
                    required={type === 'create'}
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                    placeholder="••••••••••••"
                  />
                </div>
              </>
            )}

            {/* CAMPOS: LAUDO MÉDICO */}
            {entity === 'laudo' && (
              <>
                <div className="grid grid-cols-2 gap-3">
                  <div className="col-span-2 sm:col-span-1">
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Paciente Completo *</label>
                    <input
                      type="text"
                      required
                      value={nombres}
                      onChange={(e) => setNombres(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="Nome do paciente"
                    />
                  </div>
                  <div className="col-span-2 sm:col-span-1">
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Documento / CPF *</label>
                    <input
                      type="text"
                      required
                      value={documento}
                      onChange={(e) => setDocumento(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                      placeholder="000.000.000-00"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div className="col-span-2 sm:col-span-1">
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Data do Exame *</label>
                    <input
                      type="date"
                      required
                      value={fechaEstudio}
                      onChange={(e) => setFechaEstudio(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none"
                    />
                  </div>
                  <div className="col-span-2 sm:col-span-1">
                    <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Estudo / Exame *</label>
                    <div className="relative">
                      <select
                        value={tipoEstudio}
                        onChange={(e) => setTipoEstudio(e.target.value)}
                        className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none appearance-none"
                      >
                        <option value="Eletroencefalograma (EEG)">Eletroencefalograma (EEG)</option>
                        <option value="Polissonografia">Polissonografia</option>
                        <option value="Eletroneuromiografia (ENMG)">Eletroneuromiografia (ENMG)</option>
                        <option value="Potencial Evocado">Potencial Evocado</option>
                        <option value="Mapeamento Cerebral">Mapeamento Cerebral</option>
                      </select>
                      <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-405">
                        <svg className="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                      </div>
                    </div>
                  </div>
                </div>

                <div>
                  <label className="block text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1.5">Clínica Origem *</label>
                  <div className="relative">
                    <select
                      value={idClinica}
                      onChange={(e) => setIdClinica(e.target.value)}
                      className="block w-full px-3.5 py-2.5 bg-slate-50 hover:bg-slate-100/50 focus:bg-white border-2 border-slate-100 focus:border-cyan-500 rounded-xl text-sm font-medium transition-all outline-none appearance-none"
                    >
                      {clinicas.map(c => (
                        <option key={c.id} value={c.id}>{c.NOMBRE}</option>
                      ))}
                    </select>
                    <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-405">
                      <svg className="fill-current h-4 w-4" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                  </div>
                </div>
              </>
            )}

          </div>

          {/* Botones del Modal */}
          <div className="bg-slate-50 px-6 py-4 flex justify-end gap-2.5 border-t border-slate-200">
            <button
              type="button"
              className="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-100 font-bold text-xs transition-all active:scale-95"
              onClick={onClose}
            >
              Cancelar
            </button>
            <button
              type="submit"
              className="px-5 py-2.5 bg-gradient-to-r from-[#1a9fc9] to-[#0d7fa3] text-white rounded-xl hover:opacity-90 font-bold text-xs shadow-md shadow-cyan-500/10 transition-all transform active:scale-95"
            >
              Salvar Dados
            </button>
          </div>
        </form>

      </div>
    </div>
  );
}