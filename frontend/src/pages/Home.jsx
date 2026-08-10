import HomepageSectionRenderer from "../components/homepage/HomepageSectionRenderer";
import { useSiteConfig } from "../context/siteconfigcontext";

export default function Home() {
  const { homepage_sections: sections, loaded } = useSiteConfig();

  if (!loaded) {
    return <div className="min-h-[60vh]" />;
  }

  return (
    <>
      {(sections || []).map((section) => (
        <HomepageSectionRenderer key={section.key} section={section} />
      ))}
    </>
  );
}
