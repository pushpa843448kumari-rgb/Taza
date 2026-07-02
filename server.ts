import express from "express";

const app = express();
const PORT = 3000;

app.get("*", (req, res) => {
  res.send(`
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title>Quick Kart - PHP App</title>
      <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-slate-50 min-h-screen flex items-center justify-center p-6 text-center select-none relative overflow-hidden">
      <!-- Decorative blobs -->
      <div class="absolute top-0 left-0 w-64 h-64 bg-orange-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 -translate-x-1/2 -translate-y-1/2"></div>
      <div class="absolute bottom-0 right-0 w-80 h-80 bg-amber-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 translate-x-1/3 translate-y-1/3"></div>

      <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_8px_30px_rgba(0,0,0,0.04)] border border-slate-100 max-w-xl relative z-10">
        <div class="w-20 h-20 bg-gradient-to-tr from-orange-500 to-amber-500 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-orange-500/30 transform rotate-3">
            <i class="fa-solid fa-code text-4xl text-white"></i>
        </div>
        <h1 class="text-3xl font-black text-slate-800 mb-3 tracking-tight">Quick Kart PHP</h1>
        <p class="text-slate-500 font-medium mb-8">
          Your full-stack PHP + MySQL E-commerce application has been successfully generated and styled with the <strong class="text-orange-500">Vibrant Palette</strong> theme.
        </p>
        
        <div class="bg-orange-50/50 border border-orange-100 p-6 rounded-3xl text-sm text-left mb-8">
          <p class="mb-4 text-slate-700 font-medium flex items-start"><i class="fa-solid fa-circle-info text-orange-500 mt-1 mr-3 text-lg"></i> <span>Because this environment runs Node.js, the live preview cannot execute PHP files or run MySQL directly.</span></p>
          <p class="text-slate-700 font-medium flex items-start"><i class="fa-solid fa-download text-orange-500 mt-1 mr-3 text-lg"></i> <span>To view and test your styled app, click the <strong>gear icon (Settings)</strong> in the top right, select <strong>Download ZIP</strong> or <strong>Export to GitHub</strong>, and run it on your own PHP/MySQL server (like XAMPP, WAMP, or cPanel hosting).</span></p>
        </div>
        
        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Run <code class="bg-slate-100 text-slate-600 px-2 py-1 rounded-md mx-1 font-mono">install.php</code> after downloading to auto-create the database and tables.</p>
      </div>
    </body>
    </html>
  `);
});

app.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running on port ${PORT}`);
});

