import { useState, useRef, useEffect } from 'react';

const knowledgeBase = [
  {
    keywords: ['ppid', 'informasi publik', 'permohonan informasi'],
    answer: 'Kunjungi menu PPID di website ini atau Anda bisa datang langsung ke Gedung Bale Upakari Lantai 3. 😊',
  },
  {
    keywords: ['skm', 'survei', 'kepuasan'],
    answer: 'Survei Kepuasan Masyarakat (SKM) bisa diisi lewat menu SKM di navbar, atau klik banner hijau di bawah chat ini.',
  },
  {
    keywords: ['aduan', 'lapor', 'ulas', 'komplain', 'pengaduan'],
    answer: 'Untuk pengaduan, silakan gunakan layanan ULAS. Tim kami akan menindaklanjuti laporan Anda secepatnya.',
  },
  {
    keywords: ['biaya', 'gratis', 'tarif', 'bayar'],
    answer: 'Seluruh layanan informasi publik di Diskominfo SP Kota Surakarta tidak dipungut biaya alias gratis.',
  },
  {
    keywords: ['jam', 'buka', 'operasional', 'waktu'],
    answer: 'Layanan kami buka Senin–Jumat, pukul 08.00–16.00 WIB, di Gedung Bale Upakari Lantai 3.',
  },
  {
    keywords: ['alamat', 'lokasi', 'kantor', 'dimana'],
    answer: 'Kantor kami berada di Gedung Bale Upakari Lantai 3, Jl. Jenderal Sudirman No. 2, Kompleks Balaikota Surakarta.',
  },
];

function getBotResponse(userMessage) {
  const lower = userMessage.toLowerCase();
  const match = knowledgeBase.find((item) =>
    item.keywords.some((kw) => lower.includes(kw))
  );
  return match
    ? match.answer
    : 'Maaf, saya belum punya jawaban untuk itu. Silakan hubungi (0271) 806060 atau lihat menu FAQ di samping ya.';
}

export default function HelpSection() {
  const [messages, setMessages] = useState([
    { from: 'bot', text: 'Halo! Saya MONIKS, asisten virtual Diskominfo SP Surakarta. Ada yang bisa saya bantu hari ini?' },
  ]);
  const [input, setInput] = useState('');
  const [isTyping, setIsTyping] = useState(false);
  const scrollRef = useRef(null);

  useEffect(() => {
    scrollRef.current?.scrollTo({ top: scrollRef.current.scrollHeight, behavior: 'smooth' });
  }, [messages, isTyping]);

  const handleSend = () => {
    const trimmed = input.trim();
    if (!trimmed) return;

    setMessages((prev) => [...prev, { from: 'user', text: trimmed }]);
    setInput('');
    setIsTyping(true);

    setTimeout(() => {
      const reply = getBotResponse(trimmed);
      setMessages((prev) => [...prev, { from: 'bot', text: reply }]);
      setIsTyping(false);
    }, 600);
  };

  const handleKeyDown = (e) => {
    if (e.key === 'Enter') handleSend();
  };

  return (
    <section className="py-14 bg-slate-50/50">
      <div className="max-w-7xl mx-auto px-6">
        <div className="text-center mb-10">
          <div className="inline-flex items-center gap-2 bg-white text-accent-700 px-4 py-1.5 rounded-full mb-4 font-bold text-[10px] uppercase border border-accent-100 shadow-sm">
             <span className="w-1.5 h-1.5 bg-accent-600 rounded-full animate-bounce"></span> Pusat Bantuan
          </div>
          <h2 className="text-2xl font-bold text-slate-800 mb-2 tracking-tight">Pusat Bantuan & Interaksi</h2>
          <p className="text-slate-400 font-medium text-sm">Temukan jawaban atau hubungi asisten virtual kami</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* 1. Chat BOT MONIKS */}
          <div className="bg-white rounded-2xl border border-accent-100 shadow-md overflow-hidden flex flex-col h-[450px]">
             <div className="bg-primary p-5 text-white flex justify-between items-center shrink-0">
                <div className="flex gap-3 items-center">
                   <div className="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center text-lg shadow-inner"><i className="bi bi-robot"></i></div>
                   <div>
                      <h4 className="font-bold text-sm tracking-tight">Tanya MONIKS</h4>
                      <p className="text-[10px] opacity-80 flex items-center gap-1 font-bold">
                        <span className="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> ASISTEN VIRTUAL ONLINE
                      </p>
                   </div>
                </div>
             </div>

             <div ref={scrollRef} className="flex-1 p-5 space-y-3 overflow-y-auto bg-slate-50/50">
                {messages.map((msg, i) =>
                  msg.from === 'bot' ? (
                    <div key={i} className="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm text-xs text-slate-600 border border-accent-50 leading-relaxed max-w-[85%]">
                      {msg.text}
                    </div>
                  ) : (
                    <div key={i} className="bg-primary p-3 rounded-2xl rounded-tr-none shadow-sm text-xs text-white leading-relaxed max-w-[80%] ml-auto font-medium">
                      {msg.text}
                    </div>
                  )
                )}
                {isTyping && (
                  <div className="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm text-xs text-slate-400 border border-accent-50 max-w-[50%] italic">
                    Mengetik...
                  </div>
                )}
             </div>

             <div className="p-3 border-t border-accent-50 bg-white flex gap-2 shrink-0">
                <input
                  value={input}
                  onChange={(e) => setInput(e.target.value)}
                  onKeyDown={handleKeyDown}
                  className="flex-1 bg-slate-100 border-none rounded-xl px-4 text-xs font-medium focus:ring-1 focus:ring-primary/20 transition-all"
                  placeholder="Ketik pertanyaan Anda..."
                />
                <button
                  onClick={handleSend}
                  disabled={!input.trim()}
                  className="w-10 h-10 shrink-0 bg-primary text-white rounded-xl flex items-center justify-center shadow-sm active:scale-90 transition disabled:opacity-40 disabled:active:scale-100"
                >
                  <i className="bi bi-send-fill text-sm"></i>
                </button>
             </div>
          </div>

          {/* 2. FAQ & SKM Link */}
          {/* PERUBAHAN: Menambahkan h-full pada div pembungkus kanan agar tingginya maksimal */}
          <div className="lg:col-span-2 flex flex-col gap-4 h-full">
             <div className="bg-white p-5 rounded-xl border border-accent-100 flex justify-between items-center group cursor-pointer hover:bg-accent-50 transition">
                <div className="flex items-center gap-5">
                   <div className="w-11 h-11 bg-orange-100 text-orange-500 rounded-2xl flex items-center justify-center text-lg"><i className="bi bi-question-circle-fill"></i></div>
                   <div>
                      <h4 className="font-bold text-slate-800 text-sm uppercase tracking-tight">FAQ</h4>
                      <p className="text-xs text-slate-400 font-bold">Pertanyaan yang sering diajukan</p>
                   </div>
                </div>
                <i className="bi bi-chevron-down text-slate-300 group-hover:text-primary transition"></i>
             </div>

             <div className="space-y-2.5">
                {['Layanan apa saja yang tersedia di Diskominfo?', 'Apakah layanan informasi publik dikenakan biaya?', 'Bagaimana cara melaporkan aduan melalui ULAS?'].map((q, i) => (
                   <div key={i} className="bg-white p-4 px-6 rounded-2xl border border-accent-50 text-xs font-bold text-slate-600 flex justify-between items-center hover:border-primary/20 transition-colors cursor-pointer group">
                      {q} <i className="bi bi-plus-lg text-slate-300 group-hover:text-primary"></i>
                   </div>
                ))}
             </div>

             {/* PERUBAHAN: Menambahkan mt-auto agar banner SKM didorong ke bagian paling bawah kolom */}
             <a href="/skm" className="bg-emerald-500 p-6 rounded-2xl text-white flex justify-between items-center mt-auto shadow-sm group">
                <div className="flex items-center gap-5">
                   <div className="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl"><i className="bi bi-hand-thumbs-up-fill"></i></div>
                   <div>
                      <h4 className="font-bold text-base tracking-tight leading-none">Survei Kepuasan Masyarakat (SKM)</h4>
                      <p className="text-xs opacity-80 mt-2 font-bold uppercase tracking-widest">Berikan penilaian terhadap layanan kami</p>
                   </div>
                </div>
                <div className="w-9 h-9 bg-white/20 rounded-full flex items-center justify-center group-hover:translate-x-2 transition shrink-0">
                   <i className="bi bi-arrow-right"></i>
                </div>
             </a>
          </div>
        </div>
      </div>
    </section>
  );
}