import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App";
import { ThemeProvider } from "./context/themecontext";
import { SiteConfigProvider } from "./context/siteconfigcontext";
import "./styles/tailwind.css";

ReactDOM.createRoot(document.getElementById("root")).render(
  <React.StrictMode>
    <ThemeProvider>
      <SiteConfigProvider>
        <App />
      </SiteConfigProvider>
    </ThemeProvider>
  </React.StrictMode>,
);
