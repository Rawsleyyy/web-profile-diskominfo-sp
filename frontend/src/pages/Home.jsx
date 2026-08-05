// src/pages/Home.jsx
import Hero from '../components/sections/Hero';
import LayananCepat from '../components/sections/LayananCepat';
import NewsSection from '../components/sections/NewsSection';
import MediaSection from '../components/sections/MediaSection';
import IKMSection from '../components/sections/IKMSection';
import PrestasiSection from '../components/sections/PrestasiSection';
import StructureSection from '../components/sections/StructureSection';
import HelpSection from '../components/sections/HelpSection';

export default function Home() {
  return (
    <>
      <Hero />
      <LayananCepat />
      <NewsSection />
      <StructureSection />
      <MediaSection />
      <IKMSection />
      <PrestasiSection />
      <HelpSection />
    </>
  );
}