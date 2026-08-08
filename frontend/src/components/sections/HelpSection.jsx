import { useEffect, useRef, useState } from "react";
import { Link } from "react-router-dom";

const knowledgeBase = [
  {
    keywords: ["ppid", "informasi publik", "permohonan informasi"],
    answer: "Kunjungi menu PPID di website ini atau Anda bisa datang langsung ke Gedung Bale Upakari Lantai 3. 😊",
  },
  {
    keywords: ["skm", "survei", "kepuasan"],
    answer: "Survei Kepuasan Masyarakat (SKM) bisa diisi lewat menu SKM di navbar, atau klik banner di bawah chat ini.",
  },
  {
    keywords: ["aduan", "lapor", "ulas", "komplain", "pengaduan"],
    answer: "Untuk pengaduan, silakan gunakan layanan ULAS. Tim kami akan menindaklanjuti laporan Anda secepatnya.",
  },
  {
    keywords: ["biaya", "gratis", "tarif", "bayar"],
    answer: "Seluruh layanan informasi publik di Diskominfo SP Kota Surakarta tidak dipungut biaya alias gratis.",
  },
  {
    keywords: ["jam", "buka", "operasional", "waktu"],
    answer: "Layanan kami buka Senin–Jumat, pukul 08.00–16.00 WIB, di Gedung Bale Upakari Lantai 3.",
  },
  {
    keywords: ["alamat", "lokasi", "kantor", "dimana"],
    answer: "Kantor kami berada di Gedung Bale Upakari Lantai 3, Jl. Jenderal Sudirman No. 2, Kompleks Balaikota Surakarta.",
  },
];

const faqs = [
  "Layanan apa saja yang tersedia di Diskominfo?",
  "Apakah layanan informasi publik dikenakan biaya?",
  "Bagaimana cara melaporkan aduan melalui ULAS?",
];

function getBotResponse(userMessage) {
  const lower = userMessage.toLowerCase();
  const match = knowledgeBase.find((item) =>
    item.keywords.some((keyword) => lower.includes(keyword)),
  );

  return match
    ? match.answer
    : "Maaf, saya belum punya jawaban untuk itu. Silakan hubungi (0271) 806060 atau lihat menu FAQ di samping ya.";
}

export default function HelpSection() {
  const [messages, setMessages] = useState([
    {
      from: "bot",
      text: "Halo! Saya MONIKS, asisten virtual Diskominfo SP Surakarta. Ada yang bisa saya bantu hari ini?",
    },
  ]);
  const [input, setInput] = useState("");
  const [isTyping, setIsTyping] = useState(false);
  const scrollRef = useRef(null);

  useEffect(() => {
    scrollRef.current?.scrollTo({
      top: scrollRef.current.scrollHeight,
      behavior: "smooth",
    });
  }, [messages, isTyping]);

  const handleSend = () => {
    const trimmed = input.trim();
    if (!trimmed) return;

    setMessages((current) => [...current, { from: "user", text: trimmed }]);
    setInput("");
    setIsTyping(true);

    window.setTimeout(() => {
      const reply = getBotResponse(trimmed);
      setMessages((current) => [...current, { from: "bot", text: reply }]);
      setIsTyping(false);
    }, 600);
  };

  const handleKeyDown = (event) => {
    if (event.key === "Enter") handleSend();
  };

  return (
    <section className="theme-section-alt py-16">
      <div className="mx-auto max-w-7xl px-6">
        <div className="mb-10 text-center">
          <div className="theme-chip mb-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[10px] font-bold uppercase shadow-sm">
            <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-current" />
            Pusat Bantuan
          </div>
          <h2 className="theme-text-main mb-2 text-2xl font-bold tracking-tight">
            Pusat Bantuan & Interaksi
          </h2>
          <p className="theme-text-muted text-sm font-medium">
            Temukan jawaban atau hubungi asisten virtual kami
          </p>
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="theme-card flex h-[450px] flex-col overflow-hidden rounded-2xl">
            <div className="theme-action-gradient flex shrink-0 items-center justify-between p-5 text-white">
              <div className="flex items-center gap-3">
                <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-lg shadow-inner">
                  <i className="bi bi-robot" />
                </div>
                <div>
                  <h4 className="text-sm font-bold tracking-tight">Tanya MONIKS</h4>
                  <p className="mt-0.5 flex items-center gap-1 text-[10px] font-bold text-white/72">
                    <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-300" />
                    ASISTEN VIRTUAL ONLINE
                  </p>
                </div>
              </div>
            </div>

            <div
              ref={scrollRef}
              className="flex-1 space-y-3 overflow-y-auto p-5"
              style={{ background: "var(--surface-muted)" }}
            >
              {messages.map((message, index) => (
                message.from === "bot" ? (
                  <div
                    key={`${message.from}-${index}`}
                    className="theme-card-flat max-w-[85%] rounded-2xl rounded-tl-none p-3 text-xs leading-relaxed shadow-sm"
                  >
                    {message.text}
                  </div>
                ) : (
                  <div
                    key={`${message.from}-${index}`}
                    className="theme-action ml-auto max-w-[80%] rounded-2xl rounded-tr-none p-3 text-xs font-medium leading-relaxed text-white shadow-sm"
                  >
                    {message.text}
                  </div>
                )
              ))}

              {isTyping && (
                <div className="theme-card-flat theme-text-muted max-w-[50%] rounded-2xl rounded-tl-none p-3 text-xs italic shadow-sm">
                  Mengetik...
                </div>
              )}
            </div>

            <div className="theme-card-flat flex shrink-0 gap-2 border-x-0 border-b-0 p-3">
              <input
                value={input}
                onChange={(event) => setInput(event.target.value)}
                onKeyDown={handleKeyDown}
                className="theme-input min-w-0 flex-1 rounded-xl px-4 text-xs font-medium"
                placeholder="Ketik pertanyaan Anda..."
                aria-label="Pertanyaan untuk MONIKS"
              />
              <button
                type="button"
                onClick={handleSend}
                disabled={!input.trim()}
                className="theme-action flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-sm transition active:scale-90 disabled:opacity-40 disabled:active:scale-100"
                aria-label="Kirim pertanyaan"
              >
                <i className="bi bi-send-fill text-sm" />
              </button>
            </div>
          </div>

          <div className="flex h-full flex-col gap-4 lg:col-span-2">
            <div className="theme-card-flat group flex cursor-pointer items-center justify-between rounded-2xl p-5 transition hover:-translate-y-0.5">
              <div className="flex items-center gap-5">
                <div className="theme-chip flex h-11 w-11 items-center justify-center rounded-2xl text-lg">
                  <i className="bi bi-question-circle-fill" />
                </div>
                <div>
                  <h4 className="theme-text-main text-sm font-bold uppercase tracking-tight">
                    FAQ
                  </h4>
                  <p className="theme-text-muted text-xs font-bold">
                    Pertanyaan yang sering diajukan
                  </p>
                </div>
              </div>
              <i className="bi bi-chevron-down theme-text-muted transition group-hover:translate-y-0.5" />
            </div>

            <div className="space-y-2.5">
              {faqs.map((question) => (
                <div
                  key={question}
                  className="theme-card-flat group flex cursor-pointer items-center justify-between rounded-2xl px-6 py-4 text-xs font-bold transition hover:-translate-y-0.5"
                >
                  <span className="theme-text-secondary">{question}</span>
                  <i className="bi bi-plus-lg theme-text-muted transition group-hover:rotate-90" />
                </div>
              ))}
            </div>

            <Link
              to="/skm"
              className="theme-action-gradient group mt-auto flex items-center justify-between rounded-2xl p-6 text-white shadow-sm"
            >
              <div className="flex items-center gap-5">
                <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-2xl">
                  <i className="bi bi-hand-thumbs-up-fill" />
                </div>
                <div>
                  <h4 className="text-base font-bold leading-none tracking-tight">
                    Survei Kepuasan Masyarakat (SKM)
                  </h4>
                  <p className="mt-2 text-xs font-bold uppercase tracking-widest text-white/72">
                    Berikan penilaian terhadap layanan kami
                  </p>
                </div>
              </div>
              <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15 transition group-hover:translate-x-2">
                <i className="bi bi-arrow-right" />
              </div>
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}
