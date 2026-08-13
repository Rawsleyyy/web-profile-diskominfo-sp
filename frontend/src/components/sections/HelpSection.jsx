import { useEffect, useMemo, useRef, useState } from "react";
import { Link } from "react-router-dom";
import { api } from "../../services/api";

function isExternalUrl(url = "") {
  return /^https?:\/\//i.test(url);
}

function ActionLink({ action }) {
  if (!action?.url || !action?.label) return null;

  const classes = "mt-2 inline-flex items-center gap-1 rounded-lg border border-current/15 px-2.5 py-1.5 text-[10px] font-bold transition hover:-translate-y-0.5";

  if (isExternalUrl(action.url)) {
    return (
      <a href={action.url} target="_blank" rel="noreferrer" className={classes}>
        {action.label} <i className="bi bi-box-arrow-up-right" />
      </a>
    );
  }

  return <Link to={action.url} className={classes}>{action.label} <i className="bi bi-arrow-right" /></Link>;
}

export default function HelpSection() {
  const [faqs, setFaqs] = useState([]);
  const [faqLoading, setFaqLoading] = useState(true);
  const [faqError, setFaqError] = useState(false);
  const [openFaqId, setOpenFaqId] = useState(null);
  const [messages, setMessages] = useState([
    {
      from: "bot",
      text: "Halo! Saya MONIKS. Saya membantu berdasarkan FAQ, identitas instansi, dan layanan aktif di website ini. Ada yang bisa saya bantu?",
      source: "system",
    },
  ]);
  const [input, setInput] = useState("");
  const [isTyping, setIsTyping] = useState(false);
  const scrollRef = useRef(null);

  useEffect(() => {
    let active = true;

    api.get("/faqs")
      .then(({ data }) => {
        if (!active) return;
        setFaqs(Array.isArray(data?.data) ? data.data : []);
        setFaqError(false);
      })
      .catch(() => {
        if (!active) return;
        setFaqs([]);
        setFaqError(true);
      })
      .finally(() => active && setFaqLoading(false));

    return () => { active = false; };
  }, []);

  useEffect(() => {
    scrollRef.current?.scrollTo({
      top: scrollRef.current.scrollHeight,
      behavior: "smooth",
    });
  }, [messages, isTyping]);

  const quickQuestions = useMemo(() => faqs.slice(0, 3), [faqs]);

  const sendQuestion = async (rawQuestion) => {
    const trimmed = String(rawQuestion || "").trim();
    if (!trimmed || isTyping) return;

    setMessages((current) => [...current, { from: "user", text: trimmed }]);
    setInput("");
    setIsTyping(true);

    try {
      const { data } = await api.post("/moniks/ask", { message: trimmed });
      const reply = data?.data || {};
      setMessages((current) => [
        ...current,
        {
          from: "bot",
          text: reply.answer || "Maaf, jawaban belum tersedia.",
          source: reply.source || "fallback",
          action: reply.action || null,
        },
      ]);
    } catch {
      setMessages((current) => [
        ...current,
        {
          from: "bot",
          text: "Maaf, MONIKS sedang tidak dapat menghubungi basis informasi. Silakan gunakan FAQ di samping atau coba lagi beberapa saat.",
          source: "error",
        },
      ]);
    } finally {
      setIsTyping(false);
    }
  };

  const handleSend = () => sendQuestion(input);

  const handleKeyDown = (event) => {
    if (event.key === "Enter" && !event.shiftKey) {
      event.preventDefault();
      handleSend();
    }
  };

  return (
    <section className="theme-section-alt py-16">
      <div className="mx-auto max-w-7xl px-6">
        <div className="mb-10 text-center">
          <div className="theme-chip mb-4 inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-[10px] font-bold uppercase shadow-sm">
            <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-current" />
            Pusat Bantuan
          </div>
          <h2 className="theme-text-main mb-2 text-2xl font-bold tracking-tight">Pusat Bantuan & Interaksi</h2>
          <p className="theme-text-muted text-sm font-medium">FAQ resmi dan asisten virtual berbasis informasi website</p>
        </div>

        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <div className="theme-card flex h-[500px] flex-col overflow-hidden rounded-2xl">
            <div className="theme-action-gradient flex shrink-0 items-center justify-between p-5 text-white">
              <div className="flex items-center gap-3">
                <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-lg shadow-inner"><i className="bi bi-robot" /></div>
                <div>
                  <h4 className="text-sm font-bold tracking-tight">Tanya MONIKS</h4>
                  <p className="mt-0.5 flex items-center gap-1 text-[10px] font-bold text-white/72">
                    <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-300" />
                    BASIS INFORMASI RESMI
                  </p>
                </div>
              </div>
            </div>

            <div ref={scrollRef} className="flex-1 space-y-3 overflow-y-auto p-5" style={{ background: "var(--surface-muted)" }}>
              {messages.map((message, index) => (
                message.from === "bot" ? (
                  <div key={`${message.from}-${index}`} className="theme-card-flat max-w-[88%] rounded-2xl rounded-tl-none p-3 text-xs leading-relaxed shadow-sm">
                    <p>{message.text}</p>
                    <ActionLink action={message.action} />
                  </div>
                ) : (
                  <div key={`${message.from}-${index}`} className="theme-action ml-auto max-w-[82%] rounded-2xl rounded-tr-none p-3 text-xs font-medium leading-relaxed text-white shadow-sm">
                    {message.text}
                  </div>
                )
              ))}

              {messages.length === 1 && quickQuestions.length > 0 && (
                <div className="space-y-1.5 pt-1">
                  <p className="theme-text-muted text-[10px] font-bold uppercase tracking-wide">Coba tanyakan</p>
                  {quickQuestions.map((faq) => (
                    <button
                      key={faq.id}
                      type="button"
                      onClick={() => sendQuestion(faq.question)}
                      className="theme-card-flat block w-full rounded-xl px-3 py-2 text-left text-[10px] font-semibold transition hover:-translate-y-0.5"
                    >
                      {faq.question}
                    </button>
                  ))}
                </div>
              )}

              {isTyping && <div className="theme-card-flat theme-text-muted max-w-[50%] rounded-2xl rounded-tl-none p-3 text-xs italic shadow-sm">MONIKS mencari informasi...</div>}
            </div>

            <div className="theme-card-flat flex shrink-0 gap-2 border-x-0 border-b-0 p-3">
              <input
                value={input}
                onChange={(event) => setInput(event.target.value)}
                onKeyDown={handleKeyDown}
                disabled={isTyping}
                className="theme-input min-w-0 flex-1 rounded-xl px-4 text-xs font-medium disabled:opacity-60"
                placeholder="Contoh: bagaimana cara mengadu?"
                aria-label="Pertanyaan untuk MONIKS"
              />
              <button
                type="button"
                onClick={handleSend}
                disabled={!input.trim() || isTyping}
                className="theme-action flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-sm transition active:scale-90 disabled:opacity-40 disabled:active:scale-100"
                aria-label="Kirim pertanyaan"
              >
                <i className="bi bi-send-fill text-sm" />
              </button>
            </div>
          </div>

          <div className="flex h-full flex-col gap-4 lg:col-span-2">
            <div className="theme-card-flat flex items-center justify-between rounded-2xl p-5">
              <div className="flex items-center gap-5">
                <div className="theme-chip flex h-11 w-11 items-center justify-center rounded-2xl text-lg"><i className="bi bi-question-circle-fill" /></div>
                <div>
                  <h4 className="theme-text-main text-sm font-bold uppercase tracking-tight">FAQ</h4>
                  <p className="theme-text-muted text-xs font-bold">Klik pertanyaan untuk melihat jawaban resmi</p>
                </div>
              </div>
              <span className="theme-text-muted rounded-full border border-current/10 px-2.5 py-1 text-[10px] font-bold">{faqs.length} FAQ</span>
            </div>

            <div className="space-y-2.5">
              {faqLoading && [1, 2, 3].map((item) => <div key={item} className="theme-card-flat h-14 animate-pulse rounded-2xl" />)}

              {!faqLoading && faqError && (
                <div className="theme-card-flat rounded-2xl px-6 py-5 text-xs theme-text-muted">FAQ belum dapat dimuat. Pastikan migration dan API sudah aktif.</div>
              )}

              {!faqLoading && !faqError && faqs.length === 0 && (
                <div className="theme-card-flat rounded-2xl px-6 py-5 text-xs theme-text-muted">Belum ada FAQ aktif dari dashboard.</div>
              )}

              {faqs.map((faq) => {
                const opened = openFaqId === faq.id;
                return (
                  <div key={faq.id} className="theme-card-flat overflow-hidden rounded-2xl transition">
                    <button
                      type="button"
                      onClick={() => setOpenFaqId(opened ? null : faq.id)}
                      className="flex w-full items-center justify-between gap-4 px-6 py-4 text-left text-xs font-bold"
                      aria-expanded={opened}
                    >
                      <span className="theme-text-secondary">{faq.question}</span>
                      <i className={`bi ${opened ? "bi-dash-lg" : "bi-plus-lg"} theme-text-muted shrink-0 transition`} />
                    </button>
                    {opened && (
                      <div className="border-t border-black/5 px-6 pb-5 pt-4 dark:border-white/10">
                        <p className="theme-text-muted text-xs leading-6">{faq.answer}</p>
                        <button type="button" onClick={() => sendQuestion(faq.question)} className="theme-chip mt-3 inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-[10px] font-bold">
                          Tanya ke MONIKS <i className="bi bi-chat-dots" />
                        </button>
                      </div>
                    )}
                  </div>
                );
              })}
            </div>

            <Link to="/skm" className="theme-action-gradient group mt-auto flex items-center justify-between rounded-2xl p-6 text-white shadow-sm">
              <div className="flex items-center gap-5">
                <div className="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 text-2xl"><i className="bi bi-hand-thumbs-up-fill" /></div>
                <div>
                  <h4 className="text-base font-bold leading-none tracking-tight">Survei Kepuasan Masyarakat (SKM)</h4>
                  <p className="mt-2 text-xs font-bold uppercase tracking-widest text-white/72">Berikan penilaian terhadap layanan kami</p>
                </div>
              </div>
              <div className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15 transition group-hover:translate-x-2"><i className="bi bi-arrow-right" /></div>
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}
