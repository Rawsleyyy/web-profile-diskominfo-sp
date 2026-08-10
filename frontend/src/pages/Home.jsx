import Hero from "../components/sections/Hero";
import LayananCepat from "../components/sections/LayananCepat";
import NewsSection from "../components/sections/NewsSection";
import MediaSection from "../components/sections/MediaSection";
import IKMSection from "../components/sections/IKMSection";
import PrestasiSection from "../components/sections/PrestasiSection";
import StructureSection from "../components/sections/StructureSection";
import HelpSection from "../components/sections/HelpSection";
import { useSiteConfig } from "../context/siteconfigcontext";

const SECTION_COMPONENTS = {
  hero: Hero,
  services: LayananCepat,
  news: NewsSection,
  structure: StructureSection,
  media: MediaSection,
  skm: IKMSection,
  awards: PrestasiSection,
  help: HelpSection,
};

export default function Home() {
  const { homepage_sections: sections } = useSiteConfig();

  return (
    <>
      {(sections || []).map((section) => {
        const Component = SECTION_COMPONENTS[section.key];
        return Component ? <Component key={section.key} /> : null;
      })}
    </>
  );
}
