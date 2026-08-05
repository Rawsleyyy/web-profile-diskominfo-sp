import { useState, useEffect } from "react";
import { Plus, Trash2, Save, ArrowLeft } from "lucide-react";
import { Link } from "react-router-dom";

export default function EditSKM() {
  // State untuk menampung daftar pertanyaan
  const [questions, setQuestions] = useState([
    { id: 1, text: "Kecepatan Pelayanan" },
    { id: 2, text: "Keramahan Petugas" }
  ]);

  // Handler: Mengubah teks pertanyaan
  const handleUpdateQuestion = (id, newText) => {
    setQuestions(questions.map(q => q.id === id ? { ...q, text: newText } : q));
  };

  // Handler: Tambah pertanyaan baru
  const addQuestion = () => {
    const newId = questions.length > 0 ? Math.max(...questions.map(q => q.id)) + 1 : 1;
    setQuestions([...questions, { id: newId, text: "" }]);
  };

  // Handler: Hapus pertanyaan
  const removeQuestion = (id) => {
    setQuestions(questions.filter(q => q.id !== id));
  };

  return (
    <div className="min-h-screen bg-[#f4f9ff] p-8 font-sans">
      <div className="max-w-4xl mx-auto">
        
        {/* Header Modul */}
        <div className="flex items-center justify-between mb-8">
          <div className="flex items-center gap-4">
            <Link to="/admin/dashboard" className="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50">
              <ArrowLeft size={20} className="text-primary" />
            </Link>
            <div>
              <h1 className="text-2xl font-black text-dark tracking-tight">Kelola Form SKM</h1>
              <p className="text-sm text-slate-500">Edit pertanyaan survei kepuasan masyarakat</p>
            </div>
          </div>
          
          <button 
            onClick={addQuestion}
            className="flex items-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-sky-200"
          >
            <Plus size={18} /> Tambah Pertanyaan
          </button>\n        </div>

        {/* List Pertanyaan (Editor Area) */}
        <div className="space-y-4">
          {questions.map((q, index) => (
            <div 
              key={q.id} 
              className="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-start gap-4 animate-in fade-in slide-in-from-bottom-2"
            >
              <div className="w-10 h-10 rounded-full bg-primary/5 flex items-center justify-center text-primary font-bold text-sm shrink-0">
                {index + 1}
              </div>
              
              <div className="flex-1">
                <label className="block text-[10px] font-black uppercase text-slate-400 mb-2 ml-1">Pertanyaan</label>
                <textarea
                  className="w-full p-4 bg-slate-50 border-none rounded-2xl text-dark font-medium focus:ring-2 focus:ring-primary/20 transition-all resize-none"
                  rows="2"
                  value={q.text}
                  onChange={(e) => handleUpdateQuestion(q.id, e.target.value)}
                  placeholder="Tulis pertanyaan di sini..."
                />
              </div>

              <button 
                onClick={() => removeQuestion(q.id)}
                className="mt-8 p-2 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all"
              >
                <Trash2 size={20} />
              </button>
            </div>
          ))}
        </div>

        {/* Footer Actions */}
        <div className="mt-10 flex justify-end border-t border-slate-200 pt-8">
          <button className="flex items-center gap-2 bg-primary hover:bg-dark text-white px-8 py-4 rounded-full font-black text-sm transition-all shadow-xl shadow-primary/20">
            <Save size={18} /> Simpan Semua Perubahan
          </button>
        </div>

      </div>
    </div>
  );
}