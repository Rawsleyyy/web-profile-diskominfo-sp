import { ExternalLink, PlayCircle } from "lucide-react";
import { Link } from "react-router-dom";
import Hero from "../sections/Hero";
import LayananCepat from "../sections/LayananCepat";
import NewsSection from "../sections/NewsSection";
import MediaSection from "../sections/MediaSection";
import IKMSection from "../sections/IKMSection";
import PrestasiSection from "../sections/PrestasiSection";
import StructureSection from "../sections/StructureSection";
import HelpSection from "../sections/HelpSection";

const BUILTIN_COMPONENTS = {
  hero: Hero,
  services: LayananCepat,
  news: NewsSection,
  structure: StructureSection,
  media: MediaSection,
  skm: IKMSection,
  awards: PrestasiSection,
  help: HelpSection,
};

function SmartLink({ href, children, className = "" }) {
  const url = href || "/";
  const external = /^https?:\/\//i.test(url);

  if (external) {
    return (
      <a href={url} target="_blank" rel="noreferrer" className={className}>
        {children}
      </a>
    );
  }

  return (
    <Link to={url} className={className}>
      {children}
    </Link>
  );
}

function SectionShell({ children, className = "" }) {
  return (
    <section className={`px-5 py-12 md:px-8 md:py-16 ${className}`}>
      <div className="mx-auto max-w-7xl">{children}</div>
    </section>
  );
}

function CustomContentSection({ section }) {
  const settings = section.settings || {};
  const layout = section.layout || "image_right";
  const hasImage = Boolean(settings.image_url);
  const centered = layout === "centered";
  const imageFirst = layout === "image_left";

  const text = (
    <div className={centered ? "mx-auto max-w-3xl text-center" : "min-w-0"}>
      {settings.title && (
        <h2 className="text-3xl font-black tracking-tight text-slate-900 dark:text-white md:text-4xl">
          {settings.title}
        </h2>
      )}
      {settings.subtitle && (
        <p className="mt-3 text-base font-medium leading-7 text-slate-500 dark:text-slate-300 md:text-lg">
          {settings.subtitle}
        </p>
      )}
      {settings.content && (
        <div className="mt-5 whitespace-pre-line text-[15px] leading-8 text-slate-700 dark:text-slate-200">
          {settings.content}
        </div>
      )}
      {settings.button_text && settings.button_url && (
        <SmartLink
          href={settings.button_url}
          className="mt-7 inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:opacity-95"
        >
          {settings.button_text}
          {/^(https?:)?\/\//i.test(settings.button_url) && <ExternalLink size={15} />}
        </SmartLink>
      )}
    </div>
  );

  const image = hasImage ? (
    <div className="overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-100 shadow-lg dark:border-slate-700 dark:bg-slate-800">
      <img src={settings.image_url} alt={settings.title || section.label} className="h-full max-h-[430px] w-full object-cover" loading="lazy" />
    </div>
  ) : null;

  if (centered) {
    return (
      <SectionShell>
        {image && <div className="mx-auto mb-8 max-w-4xl">{image}</div>}
        {text}
      </SectionShell>
    );
  }

  return (
    <SectionShell>
      <div className="grid items-center gap-8 lg:grid-cols-2 lg:gap-12">
        {imageFirst && image}
        {text}
        {!imageFirst && image}
      </div>
    </SectionShell>
  );
}

function PageHighlightSection({ section }) {
  const page = section.page;
  const settings = section.settings || {};

  if (!page) return null;

  const image = page.banner_url ? (
    <div className="overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-100 shadow-lg dark:border-slate-700 dark:bg-slate-800">
      <img src={page.banner_url} alt={page.title} className="h-72 w-full object-cover md:h-96" loading="lazy" />
    </div>
  ) : null;

  const text = (
    <div className={section.layout === "centered" ? "mx-auto max-w-3xl text-center" : "min-w-0"}>
      <span className="inline-flex rounded-full bg-primary/10 px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-primary">
        Sorotan
      </span>
      <h2 className="mt-4 text-3xl font-black tracking-tight text-slate-900 dark:text-white md:text-4xl">
        {settings.title || page.title}
      </h2>
      {(settings.subtitle || page.excerpt) && (
        <p className="mt-4 text-base leading-8 text-slate-500 dark:text-slate-300 md:text-lg">
          {settings.subtitle || page.excerpt}
        </p>
      )}
      <SmartLink
        href={page.url}
        className="mt-7 inline-flex items-center rounded-xl bg-primary px-5 py-3 text-sm font-bold text-white shadow-lg transition hover:-translate-y-0.5 hover:opacity-95"
      >
        {settings.button_text || "Lihat Selengkapnya"}
      </SmartLink>
    </div>
  );

  if (section.layout === "centered") {
    return (
      <SectionShell className="bg-slate-50/70 dark:bg-slate-950/30">
        {image && <div className="mx-auto mb-8 max-w-4xl">{image}</div>}
        {text}
      </SectionShell>
    );
  }

  const imageFirst = section.layout !== "image_right";

  return (
    <SectionShell className="bg-slate-50/70 dark:bg-slate-950/30">
      <div className="grid items-center gap-8 lg:grid-cols-2 lg:gap-12">
        {imageFirst && image}
        {text}
        {!imageFirst && image}
      </div>
    </SectionShell>
  );
}

function CtaSection({ section }) {
  const settings = section.settings || {};

  return (
    <SectionShell>
      <div
        className="overflow-hidden rounded-[2rem] px-6 py-10 text-center text-white shadow-xl md:px-12 md:py-14"
        style={{ background: "linear-gradient(135deg, var(--color-primary), color-mix(in srgb, var(--color-primary) 62%, var(--color-accent)))" }}
      >
        {settings.title && <h2 className="text-3xl font-black md:text-4xl">{settings.title}</h2>}
        {settings.subtitle && <p className="mx-auto mt-3 max-w-3xl text-base leading-7 text-white/80 md:text-lg">{settings.subtitle}</p>}
        {settings.button_text && settings.button_url && (
          <SmartLink
            href={settings.button_url}
            className="mt-7 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-extrabold text-slate-900 shadow-lg transition hover:-translate-y-0.5"
          >
            {settings.button_text}
            {/^(https?:)?\/\//i.test(settings.button_url) && <ExternalLink size={15} />}
          </SmartLink>
        )}
      </div>
    </SectionShell>
  );
}

function getEmbedUrl(rawUrl) {
  if (!rawUrl) return null;

  try {
    const url = new URL(rawUrl);
    const host = url.hostname.replace(/^www\./, "").toLowerCase();

    if (host === "youtu.be") {
      const id = url.pathname.split("/").filter(Boolean)[0];
      return id ? `https://www.youtube.com/embed/${id}` : null;
    }

    if (host === "youtube.com" || host === "m.youtube.com") {
      if (url.pathname === "/watch") {
        const id = url.searchParams.get("v");
        return id ? `https://www.youtube.com/embed/${id}` : null;
      }
      if (url.pathname.startsWith("/embed/")) {
        const id = url.pathname.split("/").filter(Boolean)[1];
        return id ? `https://www.youtube.com/embed/${id}` : null;
      }
    }

    if (host === "vimeo.com") {
      const id = url.pathname.split("/").filter(Boolean)[0];
      return /^\d+$/.test(id || "") ? `https://player.vimeo.com/video/${id}` : null;
    }

    if (host === "player.vimeo.com" && url.pathname.startsWith("/video/")) {
      return rawUrl;
    }
  } catch {
    return null;
  }

  return null;
}

function VideoSection({ section }) {
  const settings = section.settings || {};
  const embedUrl = getEmbedUrl(settings.video_url);

  return (
    <SectionShell className="bg-slate-50/70 dark:bg-slate-950/30">
      <div className="mx-auto max-w-5xl">
        {(settings.title || settings.subtitle) && (
          <div className="mb-7 text-center">
            {settings.title && <h2 className="text-3xl font-black tracking-tight text-slate-900 dark:text-white md:text-4xl">{settings.title}</h2>}
            {settings.subtitle && <p className="mx-auto mt-3 max-w-3xl text-base leading-7 text-slate-500 dark:text-slate-300">{settings.subtitle}</p>}
          </div>
        )}

        {embedUrl ? (
          <div className="aspect-video overflow-hidden rounded-[2rem] border border-slate-200 bg-black shadow-xl dark:border-slate-700">
            <iframe
              src={embedUrl}
              title={settings.title || "Video instansi"}
              className="h-full w-full"
              loading="lazy"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
              allowFullScreen
            />
          </div>
        ) : (
          <div className="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <PlayCircle className="mx-auto text-primary" size={42} />
            <p className="mt-3 text-sm text-slate-500">URL tidak dapat di-embed. Buka video pada sumber aslinya.</p>
            {settings.video_url && (
              <a href={settings.video_url} target="_blank" rel="noreferrer" className="mt-4 inline-flex rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white">
                Buka Video
              </a>
            )}
          </div>
        )}
      </div>
    </SectionShell>
  );
}

function SpacerSection({ section }) {
  const size = section.settings?.size || "md";
  const className = {
    sm: "h-6 md:h-8",
    md: "h-10 md:h-14",
    lg: "h-16 md:h-24",
    xl: "h-24 md:h-36",
  }[size] || "h-10 md:h-14";

  return <div aria-hidden="true" className={className} />;
}

export default function HomepageSectionRenderer({ section }) {
  if (!section) return null;

  if (!section.type || section.type === "builtin") {
    const Component = BUILTIN_COMPONENTS[section.key];
    return Component ? <Component /> : null;
  }

  switch (section.type) {
    case "custom_content":
      return <CustomContentSection section={section} />;
    case "page_highlight":
      return <PageHighlightSection section={section} />;
    case "cta":
      return <CtaSection section={section} />;
    case "video":
      return <VideoSection section={section} />;
    case "spacer":
      return <SpacerSection section={section} />;
    default:
      return null;
  }
}
