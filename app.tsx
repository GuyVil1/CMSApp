import { Button } from "./components/ui/button";
import { Card, CardContent } from "./components/ui/card";
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from "./components/ui/carousel";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./components/ui/tabs";
import { Badge } from "./components/ui/badge";
import { ImageWithFallback } from "./components/figma/ImageWithFallback";
import { SidebarAdBanner } from "./components/SidebarAd";
import { Play, Calendar, User, MessageCircle, Heart, Share } from "lucide-react";

// Données mock pour les articles
const featuredArticles = [
  {
    id: 1,
    title: "Test de la nouvelle console PlayStation 6",
    image: "https://images.unsplash.com/photo-1694857692314-7147cec858db?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
  },
  {
    id: 2,
    title: "Cyberpunk 2078 : Date de sortie révélée",
    image: "https://images.unsplash.com/photo-1731865283223-04f577b3e9b2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
  },
  {
    id: 3,
    title: "Tournoi eSports mondial 2025",
    image: "https://images.unsplash.com/photo-1675310854573-c5c8e4089426?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxlc3BvcnRzJTIwZ2FtaW5nJTIwdG91cm5hbWVudHxlbnwxfHx8fDE3NTU3NTYwMDd8MA&ixlib=rb-4.1.0&q=80&w=1080"
  },
  {
    id: 4,
    title: "Retour de l'arcade",
    image: "https://images.unsplash.com/photo-1698273300787-f698a50bb250?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxyZXRybyUyMGdhbWluZyUyMGFyY2FkZXxlbnwxfHx8fDE3NTU4MDU4OTd8MA&ixlib=rb-4.1.0&q=80&w=1080"
  },
  {
    id: 5,
    title: "Setup gaming ultime",
    image: "https://images.unsplash.com/photo-1636914011676-039d36b73765?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnYW1pbmclMjBzZXR1cCUyMHBjfGVufDF8fHx8MTc1NTgwNTg5N3ww&ixlib=rb-4.1.0&q=80&w=1080"
  },
  {
    id: 6,
    title: "Guide d'achat manettes 2025",
    image: "https://images.unsplash.com/photo-1694857692314-7147cec858db?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
  }
];

const articles = [
  {
    id: 1,
    title: "Un jeu Rayman qui aurait été annulé refait surface, logos à l'appui",
    excerpt: "Démembré pour de bon",
    date: "23/08/2025",
    author: "GameReporter",
    category: "NEWS",
    image: "https://images.unsplash.com/photo-1694857692314-7147cec858db?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
  },
  {
    id: 2,
    title: "Il y a 14 ans, Deus Ex : Human Revolution ressuscitait le pape de l'immersive sim",
    excerpt: "Le futur, c'est le passé",
    date: "23/08/2025",
    author: "TechGamer",
    category: "TEST",
    image: "https://images.unsplash.com/photo-1731865283223-04f577b3e9b2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
  },
  ...Array.from({ length: 28 }, (_, i) => ({
    id: i + 3,
    title: i % 4 === 0 
      ? "PlayStation 6 : Les premières fuites révèlent des performances époustouflantes"
      : i % 4 === 1
      ? "Cyberpunk 2078 annoncé : CD Projekt RED frappe fort"
      : i % 4 === 2
      ? "eSports : Le tournoi mondial 2025 bat tous les records d'audience"
      : "Guide d'achat : Les meilleures manettes gaming de 2025",
    excerpt: i % 3 === 0 
      ? "Une révolution technologique"
      : i % 3 === 1
      ? "L'avenir du gaming"
      : "Comparatif détaillé",
    date: "22/08/2025",
    author: "GameReporter",
    category: i % 3 === 0 ? "TEST" : i % 2 === 0 ? "NEWS" : "GUIDE",
    image: i % 4 === 0 
      ? "https://images.unsplash.com/photo-1694857692314-7147cec858db?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
      : i % 4 === 1
      ? "https://images.unsplash.com/photo-1731865283223-04f577b3e9b2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
      : i % 4 === 2
      ? "https://images.unsplash.com/photo-1675310854573-c5c8e4089426?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxlc3BvcnRzJTIwZ2FtaW5nJTIwdG91cm5hbWVudHxlbnwxfHx8fDE3NTU3NTYwMDd8MA&ixlib=rb-4.1.0&q=80&w=1080"
      : "https://images.unsplash.com/photo-1698273300787-f698a50bb250?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxyZXRybyUyMGdhbWluZyUyMGFyY2FkZXxlbnwxfHx8fDE3NTU4MDU4OTd8MA&ixlib=rb-4.1.0&q=80&w=1080"
  }))
];

const trailers = [
  { id: 1, title: "Final Fantasy XVII - Trailer officiel", duration: "2:34" },
  { id: 2, title: "Call of Duty 2025 - Gameplay", duration: "4:12" },
  { id: 3, title: "Mario Kart Ultimate - Annonce", duration: "1:58" },
  { id: 4, title: "Zelda: Echoes of Time - Teaser", duration: "3:21" },
  { id: 5, title: "Assassin's Creed Origins - Bande-annonce", duration: "2:45" }
];

export default function App() {
  return (
    <div className="min-h-screen bg-background">
      {/* Bannière avec thème belge */}
      <header className="border-b-2 border-belgium-yellow bg-gradient-to-r from-primary via-secondary to-tertiary shadow-lg">
        <div className="container mx-auto px-4 py-4 flex items-center justify-between">
          <div className="flex items-center space-x-3">
            <div className="w-12 h-12 bg-belgium-yellow rounded-lg flex items-center justify-center border-2 border-belgium-black">
              <span className="text-belgium-black font-bold text-xl">🎮</span>
            </div>
            <div>
              <span className="text-xl font-bold text-white">GameNews</span>
              <div className="text-belgium-yellow text-xs font-semibold">🇧🇪 BELGIQUE</div>
            </div>
          </div>
          
          <h1 className="text-2xl font-bold text-white hidden md:block text-center">
            L'actualité jeux vidéo en Belgique
          </h1>
          
          <Button className="bg-belgium-red hover:bg-belgium-red/90 text-white border-0 font-semibold">
            Se connecter
          </Button>
        </div>
      </header>

      {/* Layout principal avec bannières latérales */}
      <div className="flex justify-center min-h-screen">
        {/* Bannière gauche - Visible uniquement sur très grands écrans */}
        <aside className="hidden 2xl:block w-80 p-4 sticky top-4 h-fit">
          <SidebarAdBanner />
        </aside>

        {/* Contenu principal - 75% de largeur, centré */}
        <main className="w-3/4 px-4 py-6 space-y-8">
          {/* Section Articles en avant */}
          <section>
            <div className="flex items-center space-x-3 mb-6">
              <div className="w-1 h-8 bg-belgium-yellow"></div>
              <h2 className="text-3xl font-bold text-primary">Articles en avant</h2>
              <div className="w-1 h-8 bg-belgium-red"></div>
            </div>
            
            <div className="grid grid-cols-3 max-h-[80vh] rounded-lg overflow-hidden border-2 border-border shadow-lg">
              {/* Colonne 1 (2/3 de la largeur) */}
              <div className="col-span-2 grid grid-rows-3 h-[60vh]">
                {/* Rangée A (2/3 de la hauteur) - Article principal */}
                <div className="row-span-2 overflow-hidden group cursor-pointer hover:shadow-lg transition-shadow border-r-2 border-border">
                  <div className="relative h-full">
                    <ImageWithFallback 
                      src={featuredArticles[0].image}
                      alt={featuredArticles[0].title}
                      className="w-full h-full object-cover"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent" />
                    <div className="absolute bottom-4 left-4 right-4">
                      <Badge className="mb-2 bg-belgium-yellow text-belgium-black hover:bg-belgium-yellow/90 font-semibold">
                        À la une
                      </Badge>
                      <h3 className="text-lg font-bold text-white mb-1">{featuredArticles[0].title}</h3>
                      <p className="text-gray-200 text-xs">Lorem ipsum dolor sit amet...</p>
                    </div>
                  </div>
                </div>
                
                {/* Rangée B (1/3 de la hauteur) - Divisée en 2 colonnes */}
                <div className="grid grid-cols-2 border-r-2 border-t-2 border-border">
                  {featuredArticles.slice(1, 3).map((article, index) => (
                    <div key={article.id} className={`overflow-hidden group cursor-pointer hover:shadow-lg transition-shadow relative ${index === 0 ? 'border-r-2 border-border' : ''}`}>
                      <div className="relative h-full">
                        <ImageWithFallback 
                          src={article.image}
                          alt={article.title}
                          className="w-full h-full object-cover"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
                        <div className="absolute bottom-2 left-2 right-2">
                          <h4 className="font-semibold text-xs text-white line-clamp-2">{article.title}</h4>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
              
              {/* Colonne 2 (1/3 de la largeur) - Divisée en 3 rangées */}
              <div className="grid grid-rows-3 h-[60vh]">
                {featuredArticles.slice(3, 6).map((article, index) => (
                  <div key={article.id} className={`overflow-hidden group cursor-pointer hover:shadow-lg transition-shadow relative ${index < 2 ? 'border-b-2 border-border' : ''}`}>
                    <div className="relative h-full">
                      <ImageWithFallback 
                        src={article.image}
                        alt={article.title}
                        className="w-full h-full object-cover"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent" />
                      <div className="absolute bottom-2 left-2 right-2">
                        <h4 className="font-semibold text-xs text-white line-clamp-2">{article.title}</h4>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </section>

          {/* Section News */}
          <section>
            <div className="flex items-center space-x-3 mb-4">
              <div className="w-1 h-8 bg-belgium-red"></div>
              <h2 className="text-3xl font-bold text-primary">Dernières news</h2>
              <div className="w-1 h-8 bg-belgium-yellow"></div>
            </div>
            
            <div className="grid grid-cols-3 gap-6">
              {/* Colonne A (2/3) - Carousel avec articles */}
              <div className="col-span-2">
                <Tabs defaultValue="page1" className="w-full">
                  <TabsList className="grid w-full grid-cols-3 mb-3 bg-muted border border-border">
                    <TabsTrigger value="page1" className="data-[state=active]:bg-belgium-yellow data-[state=active]:text-belgium-black">
                      Articles 1-10
                    </TabsTrigger>
                    <TabsTrigger value="page2" className="data-[state=active]:bg-belgium-yellow data-[state=active]:text-belgium-black">
                      Articles 11-20
                    </TabsTrigger>
                    <TabsTrigger value="page3" className="data-[state=active]:bg-belgium-yellow data-[state=active]:text-belgium-black">
                      Articles 21-30
                    </TabsTrigger>
                  </TabsList>
                  
                  {[1, 2, 3].map(page => (
                    <TabsContent key={page} value={`page${page}`} className="space-y-3">
                      {articles.slice((page - 1) * 10, page * 10).map((article) => (
                        <div key={article.id} className="article-card overflow-hidden cursor-pointer rounded-lg">
                          <div className="flex">
                            {/* Image à gauche */}
                            <div className="flex-shrink-0">
                              <ImageWithFallback 
                                src={article.image}
                                alt={article.title}
                                className="w-28 h-24 object-cover rounded-l-lg"
                              />
                            </div>
                            
                            {/* Contenu à droite */}
                            <div className="flex-1 p-4 flex flex-col justify-between">
                              <div className="flex-1">
                                <div className="flex items-start justify-between mb-2">
                                  <Badge 
                                    variant="secondary"
                                    className={`text-xs px-2 py-1 font-bold uppercase tracking-wide ${
                                      article.category === 'TEST' ? 'bg-green-100 text-green-700 border-green-200' : 
                                      article.category === 'NEWS' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-purple-100 text-purple-700 border-purple-200'
                                    }`}
                                  >
                                    {article.category}
                                  </Badge>
                                  <span className="article-date ml-2">
                                    {article.date}
                                  </span>
                                </div>
                                
                                <h3 className="article-title text-lg leading-tight mb-2 line-clamp-2">
                                  {article.title}
                                </h3>
                                
                                <p className="article-excerpt text-sm">
                                  {article.excerpt}
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                      ))}
                    </TabsContent>
                  ))}
                </Tabs>
              </div>
              
              {/* Colonne B (1/3) - Trailers */}
              <div>
                <div className="flex items-center space-x-2 mb-4">
                  <Play className="w-5 h-5 text-belgium-red" />
                  <h3 className="text-xl font-semibold text-primary">Derniers trailers</h3>
                </div>
                <div className="rounded-lg overflow-hidden border-2 border-border shadow-lg">
                  {trailers.map((trailer, index) => (
                    <div key={trailer.id} className={`overflow-hidden group cursor-pointer hover:shadow-lg transition-shadow relative ${index < trailers.length - 1 ? 'border-b border-border' : ''}`}>
                      <div className="relative h-16">
                        <ImageWithFallback 
                          src="https://images.unsplash.com/photo-1731865283223-04f577b3e9b2?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx2aWRlbyUyMGdhbWUlMjBzY3JlZW5zaG90JTIwYWN0aW9ufGVufDF8fHx8MTc1NTgwNTg5Nnww&ixlib=rb-4.1.0&q=80&w=1080"
                          alt={trailer.title}
                          className="w-full h-full object-cover"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent" />
                        <div className="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                          <Play className="w-6 h-6 text-belgium-yellow" />
                        </div>
                        <div className="absolute bottom-1 right-1 bg-belgium-red text-white text-xs px-1.5 py-0.5 rounded font-semibold">
                          {trailer.duration}
                        </div>
                        <div className="absolute bottom-1 left-1 right-12">
                          <h4 className="font-semibold text-xs text-white line-clamp-1">{trailer.title}</h4>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </section>
        </main>

        {/* Bannière droite - Visible uniquement sur très grands écrans */}
        <aside className="hidden 2xl:block w-80 p-4 sticky top-4 h-fit">
          <SidebarAdBanner />
        </aside>
      </div>

      {/* Pied de page avec thème belge */}
      <footer className="bg-gradient-to-r from-primary via-secondary to-tertiary text-white mt-16">
        <div className="container mx-auto px-4 py-12">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* Colonne 1 - À propos */}
            <div className="border-l-4 border-belgium-yellow pl-4">
              <h3 className="text-xl font-semibold mb-4 text-belgium-yellow">À propos de GameNews</h3>
              <p className="text-sm opacity-90 mb-4">
                Votre source #1 pour l'actualité jeux vidéo en Belgique. Reviews, tests, guides et tout l'univers gaming depuis 2020.
              </p>
              <div className="flex space-x-4">
                <Button variant="ghost" size="sm" className="text-white hover:bg-belgium-yellow hover:text-belgium-black">
                  Twitter
                </Button>
                <Button variant="ghost" size="sm" className="text-white hover:bg-belgium-red hover:text-white">
                  YouTube
                </Button>
                <Button variant="ghost" size="sm" className="text-white hover:bg-primary hover:text-white">
                  Discord
                </Button>
              </div>
            </div>
            
            {/* Colonne 2 - Navigation */}
            <div className="border-l-4 border-belgium-red pl-4">
              <h3 className="text-xl font-semibold mb-4 text-belgium-red">Navigation</h3>
              <ul className="space-y-2 text-sm">
                <li><a href="#" className="hover:underline opacity-90 hover:text-belgium-yellow">Accueil</a></li>
                <li><a href="#" className="hover:underline opacity-90 hover:text-belgium-yellow">Tests & Reviews</a></li>
                <li><a href="#" className="hover:underline opacity-90 hover:text-belgium-yellow">Actualités</a></li>
                <li><a href="#" className="hover:underline opacity-90 hover:text-belgium-yellow">Guides</a></li>
                <li><a href="#" className="hover:underline opacity-90 hover:text-belgium-yellow">eSports</a></li>
                <li><a href="#" className="hover:underline opacity-90 hover:text-belgium-yellow">Matériel</a></li>
              </ul>
            </div>
            
            {/* Colonne 3 - Newsletter & Contact */}
            <div className="border-l-4 border-primary pl-4">
              <h3 className="text-xl font-semibold mb-4 text-white">Restez connecté 🇧🇪</h3>
              <p className="text-sm opacity-90 mb-4">
                Recevez les dernières news gaming directement dans votre boîte mail !
              </p>
              <div className="space-y-3">
                <input 
                  type="email" 
                  placeholder="Votre email..."
                  className="w-full px-3 py-2 rounded bg-white/10 border border-white/20 text-white placeholder-white/60"
                />
                <Button className="w-full bg-belgium-red hover:bg-belgium-red/90 text-white font-semibold">
                  S'abonner
                </Button>
              </div>
              <div className="mt-6 text-xs opacity-75">
                <p>📧 contact@gamenews.be</p>
                <p>📍 Bruxelles, Belgique</p>
              </div>
            </div>
          </div>
          
          <div className="border-t border-white/20 mt-8 pt-6 text-center text-sm opacity-75">
            <p>&copy; 2025 GameNews Belgium. Tous droits réservés. | Mentions légales | Politique de confidentialité</p>
            <p className="mt-2">🇧🇪 Fièrement belge - Made in Belgium</p>
          </div>
        </div>
      </footer>
    </div>
  );
}