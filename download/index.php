<?php
// Подключаем базу данных
require_once 'db.php';

$form_success = false;
$form_error = '';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $tour = trim($_POST['tour'] ?? 'Не выбран');
    $comment = trim($_POST['comment'] ?? '');

    if (!empty($name) && !empty($phone)) {
        // Сохраняем в базу
        $stmt = $conn->prepare("INSERT INTO leads (name, phone, tour, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $phone, $tour, $comment);
        
        if ($stmt->execute()) {
            $form_success = true;
            // Ссылка для WhatsApp
            $wa_text = "Здравствуйте! Заявка с сайта ОДУН ТУР.%0AИмя: " . urlencode($name) . "%0AТелефон: " . urlencode($phone) . "%0AТур: " . urlencode($tour);
            $wa_link = "https://wa.me/79991737407?text=" . $wa_text;
            
            // Открываем WhatsApp и показываем сообщение
            echo "<script>
                window.open('$wa_link', '_blank');
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'block';
                }, 500);
            </script>";
        } else {
            $form_error = 'Ошибка при сохранении: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $form_error = 'Пожалуйста, заполните имя и телефон.';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Туры в Якутию 2026 — ОДУН ТУР | Рыбалка и экскурсии</title>
<meta name="description" content="Зимние туры в Якутию 2026: северное сияние, Ленские столбы, рыбалка. Летние туры и экскурсии. Работаем из Якутска и Москвы.">
<link rel="icon" type="image/jpeg" href="https://static.tildacdn.com/tild3664-3065-4232-a539-303766373332/IMG_0439.jpeg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  /* ВАШИ СТИЛИ */
  :root{
    --dark:#1B3A4B; --dark-2:#122a37; --accent:#C77B3F; --accent-hover:#a8622b;
    --accent-soft:rgba(199,123,63,.12); --bg:#F4EFE6; --bg-soft:#EDE6D8;
    --surface:#FFFDF8; --text:#2C2C2C; --muted:#6b6b6b; --line:rgba(27,58,75,.12);
    --radius:16px; --shadow:0 10px 30px rgba(27,58,75,.10); --shadow-lg:0 20px 45px rgba(27,58,75,.16);
  }
  *{margin:0;padding:0;box-sizing:border-box}
  html{scroll-behavior:smooth}
  body{font-family:'Inter',sans-serif;color:var(--text);background:var(--bg);line-height:1.6;overflow-x:hidden}
  h1,h2,h3{font-family:'Montserrat',sans-serif;line-height:1.15}
  img{display:block;max-width:100%}
  a{text-decoration:none;color:inherit}
  button{font:inherit;cursor:pointer}
  .container{max-width:1200px;margin:0 auto;padding:0 24px}
  .section{padding:90px 0}
  .section-title{font-size:clamp(28px,4vw,44px);color:var(--dark);margin-bottom:14px;letter-spacing:-.01em}
  .section-sub{color:var(--muted);font-size:17px;margin-bottom:48px;max-width:640px}
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:15px 32px;border-radius:50px;font-weight:600;font-size:16px;cursor:pointer;border:none;transition:.25s;font-family:'Inter',sans-serif}
  .btn:focus-visible{outline:2px solid var(--accent);outline-offset:3px}
  .btn-primary{background:var(--accent);color:#fff;box-shadow:0 8px 22px rgba(199,123,63,.30)}
  .btn-primary:hover{background:var(--accent-hover);transform:translateY(-2px);box-shadow:0 14px 28px rgba(199,123,63,.42)}
  .btn-outline{background:transparent;color:#fff;border:2px solid rgba(255,255,255,.85)}
  .btn-outline:hover{background:#fff;color:var(--dark)}
  .btn-dark{background:var(--dark);color:#fff}
  .btn-dark:hover{background:var(--dark-2);transform:translateY(-2px)}

  /* HEADER */
  header{position:fixed;top:0;left:0;right:0;z-index:100;transition:.3s;padding:18px 0;background:transparent}
  header.scrolled{background:rgba(27,58,75,.97);padding:12px 0;box-shadow:0 4px 20px rgba(0,0,0,.15);backdrop-filter:blur(8px)}
  .nav{display:flex;align-items:center;justify-content:space-between;gap:20px}
  .logo-wrap{display:flex;align-items:center;gap:12px}
  .logo-img{width:46px;height:46px;object-fit:cover;border-radius:50%;border:2px solid rgba(255,255,255,.85);box-shadow:0 2px 8px rgba(0,0,0,.25);background:#fff}
  .logo-text{font-family:'Montserrat';font-weight:800;font-size:22px;color:#fff;letter-spacing:1px}
  .logo-text span{color:var(--accent)}
  .nav-links{display:flex;gap:32px;list-style:none}
  .nav-links a{color:#fff;font-weight:500;font-size:15px;transition:.2s;position:relative}
  .nav-links a:hover{color:var(--accent)}
  .nav-phone{color:#fff;font-weight:600;font-size:15px;white-space:nowrap}
  .nav-phone:hover{color:var(--accent)}
  .burger{display:none;background:none;border:none;cursor:pointer;flex-direction:column;gap:5px}
  .burger span{width:26px;height:3px;background:#fff;border-radius:2px;transition:.3s}
  .burger.open span:nth-child(1){transform:translateY(8px) rotate(45deg)}
  .burger.open span:nth-child(2){opacity:0}
  .burger.open span:nth-child(3){transform:translateY(-8px) rotate(-45deg)}

  /* MOBILE MENU */
  .mobile-menu{display:none;position:fixed;inset:0;background:var(--dark);z-index:99;flex-direction:column;align-items:center;justify-content:center;gap:28px}
  .mobile-menu.active{display:flex}
  .mobile-menu a{color:#fff;font-size:22px;font-family:'Montserrat';font-weight:600}
  .mobile-menu a:hover{color:var(--accent)}
  .mobile-menu .close{position:absolute;top:24px;right:24px;background:none;border:none;color:#fff;font-size:36px;cursor:pointer;line-height:1}

  /* HERO */
  .hero{min-height:100vh;display:flex;align-items:center;position:relative;color:#fff}
  .hero-bg{position:absolute;inset:0;background-size:cover;background-position:center;z-index:0}
  .hero-bg::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(27,58,75,.55),rgba(27,58,75,.85))}
  .hero::after{content:'';position:absolute;bottom:0;left:0;right:0;height:120px;background:linear-gradient(transparent,var(--bg));z-index:1}
  .hero-content{position:relative;z-index:2;max-width:760px;padding:120px 0 80px}
  .hero h1{font-size:clamp(36px,6vw,68px);font-weight:800;margin-bottom:22px;text-shadow:0 4px 30px rgba(0,0,0,.35)}
  .hero h1 em{font-style:normal;color:var(--accent)}
  .hero p{font-size:clamp(17px,2vw,20px);opacity:.95;margin-bottom:36px;max-width:600px}
  .hero-btns{display:flex;gap:16px;flex-wrap:wrap}
  .scroll-arrow{position:absolute;bottom:30px;left:50%;transform:translateX(-50%);z-index:2;color:#fff;font-size:28px;animation:bounce 2s infinite;text-decoration:none}
  @keyframes bounce{0%,100%{transform:translate(-50%,0)}50%{transform:translate(-50%,12px)}}

  /* WHY */
  .why-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px}
  .why-card{background:var(--surface);padding:34px 28px;border-radius:var(--radius);box-shadow:var(--shadow);transition:.3s;border:1px solid var(--line)}
  .why-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg)}
  .why-icon{width:54px;height:54px;border-radius:14px;background:var(--accent-soft);display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:18px}
  .why-card h3{font-size:19px;color:var(--dark);margin-bottom:8px}
  .why-card p{font-size:15px;color:var(--muted)}

  /* TOURS */
  .filters{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:36px}
  .filter-btn{padding:10px 22px;border-radius:50px;border:1.5px solid var(--line);background:var(--surface);font-family:'Inter';font-weight:500;font-size:14px;cursor:pointer;transition:.2s;color:var(--text)}
  .filter-btn:hover{border-color:var(--accent);color:var(--accent)}
  .filter-btn.active{background:var(--dark);color:#fff;border-color:var(--dark)}
  .tours-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:28px}
  .tour-card{background:var(--surface);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);transition:.3s;cursor:pointer;display:flex;flex-direction:column;border:1px solid var(--line)}
  .tour-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg)}
  .tour-img{height:220px;background-size:cover;background-position:center;position:relative}
  .tour-badge{position:absolute;top:14px;left:14px;background:var(--accent);color:#fff;padding:6px 14px;border-radius:50px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;box-shadow:0 4px 12px rgba(199,123,63,.4)}
  .tour-body{padding:22px;display:flex;flex-direction:column;flex:1}
  .tour-body h3{font-size:20px;color:var(--dark);margin-bottom:10px}
  .tour-meta{display:flex;gap:16px;font-size:13px;color:var(--muted);margin-bottom:16px;flex-wrap:wrap}
  .tour-price{font-family:'Montserrat';font-weight:700;font-size:20px;color:var(--accent);margin-top:auto;margin-bottom:16px}
  .tour-card .btn{width:100%;padding:12px}

  /* ABOUT */
  .about-wrap{display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center}
  .about-text p{font-size:17px;color:#4a4a4a;margin-bottom:18px}
  .facts{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:40px}
  .fact-num{font-family:'Montserrat';font-weight:800;font-size:32px;color:var(--accent)}
  .fact-label{font-size:14px;color:var(--muted)}
  .about-collage{position:relative;aspect-ratio:16/11;min-height:360px}
  .about-collage__item{position:absolute;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-lg);border:4px solid var(--surface);background:var(--bg-soft);transition:transform .4s ease, box-shadow .4s ease}
  .about-collage__item img{width:100%;height:100%;object-fit:cover;transition:transform .6s ease}
  .about-collage__item:hover{transform:translateY(-6px) rotate(0deg);box-shadow:0 24px 50px rgba(27,58,75,.22)}
  .about-collage__item:hover img{transform:scale(1.06)}
  .about-collage__item--main{top:8%;left:18%;width:64%;height:78%;z-index:3;transform:rotate(-2deg)}
  .about-collage__item--left{top:24%;left:0;width:38%;height:52%;z-index:2;transform:rotate(-8deg)}
  .about-collage__item--right{top:10%;right:0;width:34%;height:46%;z-index:2;transform:rotate(7deg)}
  .about-collage__badge{position:absolute;bottom:4%;left:6%;z-index:4;background:var(--accent);color:#fff;font-family:'Montserrat',sans-serif;font-weight:800;font-size:13px;letter-spacing:.12em;text-transform:uppercase;padding:12px 20px;border-radius:50px;box-shadow:0 10px 24px rgba(199,123,63,.45);transform:rotate(-4deg)}
  .about-collage__cta{position:absolute;bottom:4%;right:6%;z-index:4}
  .about-collage__cta .btn{padding:12px 22px;font-size:14px;box-shadow:0 12px 26px rgba(27,58,75,.35)}
  .about-collage__cta .btn:hover{transform:translateY(-3px)}

  /* GALLERY */
  .gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
  .gallery-item{position:relative;border-radius:var(--radius);overflow:hidden;aspect-ratio:4/3;cursor:pointer;background:var(--bg-soft)}
  .gallery-item img{width:100%;height:100%;object-fit:cover;transition:.5s}
  .gallery-item:hover img{transform:scale(1.08)}
  .gallery-item::after{content:'+';position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(27,58,75,.5);color:#fff;font-size:40px;opacity:0;transition:.3s}
  .gallery-item:hover::after{opacity:1}

  /* CONTACTS */
  .contacts-wrap{display:grid;grid-template-columns:1fr 1fr;gap:56px}
  .contact-info{display:flex;flex-direction:column;gap:24px}
  .contact-item{display:flex;gap:16px;align-items:flex-start}
  .contact-item .ci-icon{width:48px;height:48px;border-radius:12px;background:var(--accent-soft);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0}
  .contact-item h4{font-size:14px;color:var(--muted);font-weight:500;margin-bottom:2px}
  .contact-item a,.contact-item p{font-size:17px;font-weight:600;color:var(--dark)}
  .contact-item a:hover{color:var(--accent)}
  .socials{display:flex;gap:12px;margin-top:8px}
  .socials a{width:44px;height:44px;border-radius:50%;background:var(--dark);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;transition:.2s}
  .socials a:hover{background:var(--accent);transform:translateY(-3px)}
  .form-card{background:var(--surface);padding:40px;border-radius:var(--radius);box-shadow:var(--shadow);border:1px solid var(--line)}
  .form-card h3{font-size:24px;color:var(--dark);margin-bottom:8px}
  .form-card .form-sub{color:var(--muted);font-size:15px;margin-bottom:26px}
  .form-group{margin-bottom:18px}
  .form-group label{display:block;font-size:14px;font-weight:500;margin-bottom:6px;color:var(--dark)}
  .form-group input,.form-group textarea,.form-group select{width:100%;padding:13px 16px;border:1.5px solid var(--line);border-radius:10px;font-family:'Inter';font-size:15px;transition:.2s;background:var(--bg)}
  .form-group input:focus,.form-group textarea:focus,.form-group select:focus{outline:none;border-color:var(--accent);background:#fff;box-shadow:0 0 0 3px var(--accent-soft)}
  .form-group textarea{resize:vertical;min-height:100px}
  .form-card .btn{width:100%;margin-top:8px}
  .form-note{font-size:12px;color:var(--muted);margin-top:14px;text-align:center}
  .form-note a{color:var(--accent);text-decoration:underline}
  #success-message { display: none; background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #2e7d32; text-align: center; font-weight: 500; }

  /* FOOTER */
  footer{background:var(--dark);color:#fff;padding:56px 0 28px}
  .footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:40px;margin-bottom:40px}
  .footer-brand .logo-wrap{margin-bottom:14px}
  .footer-brand .logo-text{color:#fff}
  .footer-brand p{color:rgba(255,255,255,.7);font-size:15px;max-width:320px}
  .footer-col h4{font-size:16px;margin-bottom:16px;font-family:'Montserrat'}
  .footer-col ul{list-style:none}
  .footer-col li{margin-bottom:10px}
  .footer-col a{color:rgba(255,255,255,.75);font-size:15px;transition:.2s}
  .footer-col a:hover{color:var(--accent)}
  .footer-bottom{border-top:1px solid rgba(255,255,255,.15);padding-top:24px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:14px;color:rgba(255,255,255,.6)}
  .footer-bottom a{color:rgba(255,255,255,.6)}
  .footer-bottom a:hover{color:var(--accent)}

  /* MODALS */
  .modal{position:fixed;inset:0;background:rgba(10,20,25,.82);z-index:200;display:none;align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(6px)}
  .modal.active{display:flex;animation:fadeIn .25s ease}
  @keyframes fadeIn{from{opacity:0}to{opacity:1}}
  .modal-content{background:var(--surface);border-radius:var(--radius);max-width:760px;width:100%;max-height:90vh;overflow-y:auto;position:relative;animation:slideUp .3s ease}
  @keyframes slideUp{from{transform:translateY(30px);opacity:0}to{transform:translateY(0);opacity:1}}
  .modal-close{position:absolute;top:14px;right:18px;background:rgba(0,0,0,.5);color:#fff;border:none;width:40px;height:40px;border-radius:50%;font-size:24px;cursor:pointer;z-index:10;transition:.2s;line-height:1}
  .modal-close:hover{background:var(--accent)}
  .modal-tour-img{height:280px;background-size:cover;background-position:center}
  .modal-tour-body{padding:32px}
  .modal-tour-body h2{color:var(--dark);font-size:28px;margin-bottom:14px}
  .modal-tour-body p{color:#555;margin-bottom:18px}
  .modal-tour-body .tour-meta{margin-bottom:22px}
  .lightbox{position:fixed;inset:0;background:rgba(0,0,0,.94);z-index:300;display:none;align-items:center;justify-content:center;padding:24px}
  .lightbox.active{display:flex}
  .lightbox img{max-width:92vw;max-height:88vh;border-radius:12px}
  .lightbox-close{position:absolute;top:24px;right:32px;background:none;border:none;color:#fff;font-size:44px;cursor:pointer;line-height:1}

  /* ANIMATIONS */
  .reveal{opacity:0;transform:translateY(30px);transition:.7s ease}
  .reveal.visible{opacity:1;transform:translateY(0)}

  /* RESPONSIVE */
  @media(max-width:900px){
    .nav-links,.nav-phone{display:none}
    .burger{display:flex}
    .about-wrap,.contacts-wrap{grid-template-columns:1fr;gap:36px}
    .footer-grid{grid-template-columns:1fr 1fr}
    .about-collage{aspect-ratio:4/3;min-height:0}
    .about-collage__item--main{top:10%;left:14%;width:72%;height:74%;transform:rotate(-2deg)}
    .about-collage__item--left{top:2%;left:0;width:44%;height:44%;transform:rotate(-6deg)}
    .about-collage__item--right{bottom:2%;top:auto;right:0;width:40%;height:40%;transform:rotate(5deg)}
    .about-collage__badge{font-size:11px;padding:9px 14px;bottom:auto;top:6%;left:auto;right:4%}
    .about-collage__cta{bottom:0;right:0}
    .about-collage__cta .btn{padding:10px 16px;font-size:12px}
  }
  @media(max-width:600px){
    .section{padding:60px 0}
    .facts{grid-template-columns:1fr}
    .footer-grid{grid-template-columns:1fr}
    .hero-btns .btn{width:100%}
    .form-card{padding:26px}
    .logo-text{font-size:18px}
    .logo-img{width:40px;height:40px}
    .section-sub{margin-bottom:32px}
  }
</style>
</head>
<body>

<!-- HEADER -->
<header id="header">
  <div class="container nav">
    <a href="#top" class="logo-wrap">
      <img src="https://static.tildacdn.com/tild3664-3065-4232-a539-303766373332/IMG_0439.jpeg" alt="Логотип Одун Тур" class="logo-img">
      <span class="logo-text">ОДУН<span>ТУР</span></span>
    </a>
    <ul class="nav-links">
      <li><a href="#why">Почему мы</a></li>
      <li><a href="#tours">Туры</a></li>
      <li><a href="#about">О Якутии</a></li>
      <li><a href="#gallery">Галерея</a></li>
      <li><a href="#contacts">Контакты</a></li>
    </ul>
    <a href="tel:+79991737407" class="nav-phone">+7 999 173-74-07</a>
    <button class="burger" id="burger" aria-label="Меню" aria-expanded="false"><span></span><span></span><span></span></button>
  </div>
</header>

<!-- MOBILE MENU -->
<div class="mobile-menu" id="mobileMenu">
  <button class="close" id="menuClose" aria-label="Закрыть">&times;</button>
  <a href="#why">Почему мы</a>
  <a href="#tours">Туры</a>
  <a href="#about">О Якутии</a>
  <a href="#gallery">Галерея</a>
  <a href="#contacts">Контакты</a>
  <a href="tel:+79991737407" style="color:var(--accent)">+7 999 173-74-07</a>
</div>

<!-- HERO -->
<section class="hero" id="top">
  <div class="hero-bg" style="background-image:url('https://static.tildacdn.com/tild6664-6634-4463-a163-663733623834/47a3fad2a2dd512a13fe.jpg')"></div>
  <div class="container">
    <div class="hero-content">
      <h1>Лучшие <em>туры в Якутию</em> 2026</h1>
      <p>Организуем эксклюзивные туры в Якутию: зимняя и летняя рыбалка, экспедиции к Ленским столбам и бизонарию. Полное сопровождение на всех этапах поездки.</p>
      <div class="hero-btns">
        <a href="#tours" class="btn btn-primary">Смотреть туры →</a>
        <a href="#contacts" class="btn btn-outline">Оставить заявку</a>
      </div>
    </div>
  </div>
  <a href="#why" class="scroll-arrow" aria-label="Прокрутить вниз">↓</a>
</section>

<!-- WHY -->
<section class="section" id="why">
  <div class="container">
    <h2 class="section-title reveal">Почему выбирают наши туры по Якутии</h2>
    <p class="section-sub reveal">Мы создаём маршруты, которых нет ни у кого, и сопровождаем вас на каждом этапе.</p>
    <div class="why-grid">
      <div class="why-card reveal"><div class="why-icon">🧭</div><h3>Уникальные маршруты</h3><p>Ведём туда, где ещё не было туристов — только вы и дикая природа.</p></div>
      <div class="why-card reveal"><div class="why-icon">🛡️</div><h3>Безопасность</h3><p>Опытные гиды, снаряжение и полное сопровождение на всём пути.</p></div>
      <div class="why-card reveal"><div class="why-icon">👥</div><h3>Малые группы</h3><p>До 8 человек — внимание каждому и настоящая атмосфера.</p></div>
      <div class="why-card reveal"><div class="why-icon">🦬</div><h3>Эксклюзив</h3><p>Бизонарий — питомник лесных бизонов, которых разводят только в Якутии.</p></div>
    </div>
  </div>
</section>

<!-- TOURS -->
<section class="section" id="tours" style="background:var(--bg-soft)">
  <div class="container">
    <h2 class="section-title reveal">Зимние и летние туры в Якутию</h2>
    <p class="section-sub reveal">Выберите формат приключения — от коротких экскурсий до многодневных экспедиций.</p>
    <div class="filters reveal">
      <button class="filter-btn active" data-filter="all">Все</button>
      <button class="filter-btn" data-filter="winter">Зимние туры</button>
      <button class="filter-btn" data-filter="summer">Летние туры</button>
    </div>
    <div class="tours-grid" id="toursGrid"></div>
  </div>
</section>

<!-- ABOUT -->
<section class="section" id="about">
  <div class="container">
    <div class="about-wrap">
      <div class="about-text reveal">
        <h2 class="section-title">О Якутии (Саха Сирэ)</h2>
        <p>Республика Саха (Якутия) — субъект России, по своим природным и территориальным условиям не имеющий аналогов на планете.</p>
        <p>Именно здесь накоплен уникальный опыт ведения сельского хозяйства в экстремальных климатических условиях Севера, применены первые технологии жилищного строительства на вечной мерзлоте.</p>
        <div class="facts">
          <div><div class="fact-num">3,1 млн</div><div class="fact-label">км² площадь</div></div>
          <div><div class="fact-num">−70°C</div><div class="fact-label">полюс холода</div></div>
          <div><div class="fact-num">40%</div><div class="fact-label">за Полярным кругом</div></div>
        </div>
      </div>
      <div class="about-collage reveal" aria-label="Фотографии Якутии">
        <div class="about-collage__item about-collage__item--left">
          <img src="https://static.tildacdn.com/tild6232-3539-4232-a230-326634356434/IMG_7231.png" alt="Зимние Ленские столбы" loading="lazy">
        </div>
        <div class="about-collage__item about-collage__item--main">
          <img src="https://static.tildacdn.com/tild6664-6634-4463-a163-663733623834/47a3fad2a2dd512a13fe.jpg" alt="Природа Якутии" loading="lazy">
        </div>
        <div class="about-collage__item about-collage__item--right">
          <img src="https://static.tildacdn.com/tild3336-6465-4161-a330-663362626337/photo_3_2024-06-11_1.jpg" alt="Бизонарий в Якутии" loading="lazy">
        </div>
        <span class="about-collage__badge">Саха Сирэ</span>
        <div class="about-collage__cta">
          <a class="btn btn-primary" href="https://youtu.be/mdG1-b5W9Vo" target="_blank" rel="noopener">Смотреть видео →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- GALLERY -->
<section class="section" id="gallery" style="background:var(--bg-soft)">
  <div class="container">
    <h2 class="section-title reveal">Галерея Якутии</h2>
    <p class="section-sub reveal">Уникальные пейзажи и обитатели сурового северного края.</p>
    <div class="gallery-grid" id="galleryGrid"></div>
  </div>
</section>
    
<!-- НОВОСТИ -->
<section class="section" id="news" style="background:var(--surface)">
  <div class="container">
    <h2 class="section-title reveal">Новости и статьи</h2>
    <p class="section-sub reveal">Узнайте о новых турах, акциях и событиях в Якутии.</p>
    <div class="why-grid">
      <?php
      $news_result = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
      if ($news_result->num_rows > 0) {
          while($news = $news_result->fetch_assoc()) {

              echo '<div class="why-card reveal">';
              echo '<h3>' . htmlspecialchars($news['title']) . '</h3>';
              echo '<p style="margin-top:10px; font-size:13px; color:#C77B3F; font-weight:600;">' . date('d.m.Y', strtotime($news['created_at'])) . '</p>';
              echo '<p style="line-height:1.6;">' . nl2br(htmlspecialchars($news['content'])) . '</p>';
              echo '</div>';
          }
      } else {
          echo '<p style="color:#666;">Пока новостей нет.</p>';
      }
      ?>
    </div>
  </div>
</section>

<!-- CONTACTS -->
<section class="section" id="contacts">
  <div class="container">
    <h2 class="section-title reveal">Контакты</h2>
    <p class="section-sub reveal">Свяжитесь с нами — подберём тур под ваши даты и пожелания.</p>
    <div class="contacts-wrap">
      <div class="contact-info reveal">
        <div class="contact-item">
          <div class="ci-icon">📞</div>
          <div><h4>Телефон</h4><a href="tel:+79991737407">+7 999 173-74-07</a></div>
        </div>
        <div class="contact-item">
          <div class="ci-icon">✉️</div>
          <div><h4>Почта</h4><a href="mailto:oduntur@mail.ru">oduntur@mail.ru</a></div>
        </div>
        <div class="contact-item">
          <div class="ci-icon">📍</div>
          <div><h4>Регион</h4><p>Республика Саха (Якутия), Россия</p></div>
        </div>
        <div class="socials">
          <a href="https://wa.me/79991737407" target="_blank" rel="noopener" title="WhatsApp">WA</a>
          <a href="https://t.me/+79991737407" target="_blank" rel="noopener" title="Telegram">TG</a>
          <a href="mailto:oduntur@mail.ru" title="Почта">@</a>
        </div>
      </div>
      
      <div class="form-card reveal">
        <h3>Забронировать тур в Якутию</h3>
        <p class="form-sub">Оставьте контакты — перезвоним в течение дня.</p>
        
        <?php if ($form_error): ?>
            <div style="color:red; margin-bottom:15px;"><?= htmlspecialchars($form_error) ?></div>
        <?php endif; ?>
        
        <div id="success-message">Спасибо! Ваша заявка сохранена. Мы свяжемся с вами.</div>

        <form method="POST" action="index.php#contacts">
          <div class="form-group">
            <label for="name">Ваше имя *</label>
            <input type="text" id="name" name="name" required placeholder="Иван">
          </div>
          <div class="form-group">
            <label for="phone">Телефон *</label>
            <input type="tel" id="phone" name="phone" required placeholder="+7 999 000-00-00">
          </div>
          <div class="form-group">
            <label for="tour">Интересующий тур</label>
            <select id="tour" name="tour">
              <option value="">— Выберите —</option>
              <option>Зимние туры</option>
              <option>Летние туры</option>
              <option>Бизонарий</option>
              <option>Индивидуальный тур</option>
            </select>
          </div>
          <div class="form-group">
            <label for="comment">Комментарий</label>
            <textarea id="comment" name="comment" placeholder="Даты, количество человек, пожелания"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Отправить заявку</button>
          <p class="form-note">Нажимая кнопку, вы соглашаетесь с <a href="#">политикой конфиденциальности</a>.</p>
        </form>
      </div>

    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="#top" class="logo-wrap">
          <img src="https://static.tildacdn.com/tild3664-3065-4232-a539-303766373332/IMG_0439.jpeg" alt="Логотип Одун Тур" class="logo-img">
          <span class="logo-text">ОДУН<span>ТУР</span></span>
        </a>
        <p>Региональный туроператор. Экскурсии, экспедиции, охота и рыбалка в Республике Саха (Якутия).</p>
      </div>
      <div class="footer-col">
        <h4>Навигация</h4>
        <ul>
          <li><a href="#why">Почему мы</a></li>
          <li><a href="#tours">Туры</a></li>
          <li><a href="#about">О Якутии</a></li>
          <li><a href="#gallery">Галерея</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Контакты</h4>
        <ul>
          <li><a href="tel:+79991737407">+7 999 173-74-07</a></li>
          <li><a href="mailto:oduntur@mail.ru">oduntur@mail.ru</a></li>
          <li><a href="https://wa.me/79991737407" target="_blank" rel="noopener">WhatsApp</a></li>
          <li><a href="https://t.me/+79991737407" target="_blank" rel="noopener">Telegram</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <span id="year"></span> ОДУН ТУР. Все права защищены.</span>
      <a href="https://oduntur.ru/admin/login.php" target="_blank">Вход в админку</a>
    </div>
  </div>
</footer>

<!-- TOUR MODAL -->
<div class="modal" id="tourModal">
  <div class="modal-content">
    <button class="modal-close" onclick="closeModal('tourModal')" aria-label="Закрыть">&times;</button>
    <div class="modal-tour-img" id="modalTourImg"></div>
    <div class="modal-tour-body">
      <h2 id="modalTourTitle"></h2>
      <div class="tour-meta" id="modalTourMeta"></div>
      <p id="modalTourDesc"></p>
      <div class="tour-price" id="modalTourPrice"></div>
      <button class="btn btn-primary" onclick="closeModal('tourModal'); document.getElementById('contacts').scrollIntoView({behavior: 'smooth'});">Забронировать</button>
    </div>
  </div>
</div>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox">
  <button class="lightbox-close" onclick="closeModal('lightbox')" aria-label="Закрыть">&times;</button>
  <img src="" alt="" id="lightboxImg">
</div>

<script>
/* ===== ДАННЫЕ ТУРОВ ===== */
const TOURS = [
  { id:1, category:'winter', title:'Зимние Ленские столбы', badge:'Зима', img:'https://static.tildacdn.com/tild3731-3731-4437-b565-343434333031/IMG_9962.jpeg', days:'3 дня', difficulty:'Средний', price:'от 45 000 ₽', desc:'Зимнее путешествие к Ленским столбам — природному парку ЮНЕСКО.' },
  { id:2, category:'summer', title:'Летние туры по Якутии', badge:'Лето', img:'https://static.tildacdn.com/tild3530-6238-4639-b437-623339646439/IMG_9961.jpeg', days:'4 дня', difficulty:'Средний', price:'от 35 000 ₽', desc:'Летние маршруты по живописным местам Якутии.' },
  { id:3, category:'summer', title:'Бизонарий', badge:'Эксклюзив', img:'https://static.tildacdn.com/tild3336-6465-4161-a330-663362626337/photo_3_2024-06-11_1.jpg', days:'1 день', difficulty:'Лёгкий', price:'от 5 000 ₽', desc:'Питомник лесных бизонов.' },
  { id:4, category:'winter', title:'Зимние пейзажи Якутии', badge:'Зима', img:'https://static.tildacdn.com/tild6232-3539-4232-a230-326634356434/IMG_7231.png', days:'3 дня', difficulty:'Средний', price:'от 30 000 ₽', desc:'Снежные просторы и северное сияние.' },
  { id:5, category:'winter', title:'Северное сияние', badge:'Зима', img:'https://static.tildacdn.com/tild6530-3138-4537-b063-343938313736/IMG_7233.jpeg', days:'3 дня', difficulty:'Средний', price:'от 30 000 ₽', desc:'Охота за северным сиянием.' },
  { id:6, category:'summer', title:'Экспедиция в тайгу', badge:'Лето', img:'https://static.tildacdn.com/tild3261-6434-4539-b162-353164373061/IMG_5347.jpeg', days:'7 дней', difficulty:'Высокий', price:'от 80 000 ₽', desc:'Экспедиция вглубь якутской тайги.' }
];

/* ===== ГАЛЕРЕЯ ===== */
const GALLERY = [
  'https://static.tildacdn.com/tild6530-3138-4537-b063-343938313736/IMG_7233.jpeg',
  'https://static.tildacdn.com/tild3135-3061-4334-b938-333937353832/IMG_5344.jpeg',
  'https://static.tildacdn.com/tild3261-6434-4539-b162-353164373061/IMG_5347.jpeg',
  'https://static.tildacdn.com/tild3539-6330-4366-a139-663130356533/IMG_5410.JPEG',
  'https://static.tildacdn.com/tild3736-6138-4438-b334-613932363530/47a3fad2a2dd512a13fe.jpg',
  'https://static.tildacdn.com/tild3731-3731-4437-b565-343434333031/IMG_9962.jpeg'
];

/* ===== РЕНДЕР ТУРОВ ===== */
const toursGrid = document.getElementById('toursGrid');
function renderTours(filter='all'){
  toursGrid.innerHTML = '';
  TOURS.filter(t => filter==='all' || t.category===filter).forEach(t => {
    const card = document.createElement('div');
    card.className = 'tour-card reveal';
    card.innerHTML = `
      <div class="tour-img" style="background-image:url('${t.img}')">
        <span class="tour-badge">${t.badge}</span>
      </div>
      <div class="tour-body">
        <h3>${t.title}</h3>
        <div class="tour-meta"><span>⏱ ${t.days}</span><span>⚡ ${t.difficulty}</span></div>
        <div class="tour-price">${t.price}</div>
        <button class="btn btn-dark">Подробнее</button>
      </div>`;
    card.querySelector('.btn').addEventListener('click', () => openTourModal(t));
    toursGrid.appendChild(card);
    setTimeout(()=>card.classList.add('visible'), 50);
  });
}
renderTours();

/* Фильтры */
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    renderTours(btn.dataset.filter);
  });
});

/* ===== ГАЛЕРЕЯ ===== */
const galleryGrid = document.getElementById('galleryGrid');
GALLERY.forEach((src,i) => {
  const item = document.createElement('div');
  item.className = 'gallery-item reveal';
  item.innerHTML = `<img src="${src}" alt="Фото Якутии ${i+1}" loading="lazy">`;
  item.addEventListener('click', () => openLightbox(src));
  galleryGrid.appendChild(item);
  setTimeout(()=>item.classList.add('visible'), 50);
});

/* ===== МОДАЛКИ ===== */
function openTourModal(t){
  document.getElementById('modalTourImg').style.backgroundImage = `url('${t.img}')`;
  document.getElementById('modalTourTitle').textContent = t.title;
  document.getElementById('modalTourMeta').innerHTML = `<span>⏱ ${t.days}</span><span>⚡ ${t.difficulty}</span>`;
  document.getElementById('modalTourDesc').textContent = t.desc;
  document.getElementById('modalTourPrice').textContent = t.price;
  document.getElementById('tourModal').classList.add('active');
  document.body.style.overflow='hidden';
}
function openLightbox(src){
  document.getElementById('lightboxImg').src = src;
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow='hidden';
}
function closeModal(id){
  document.getElementById(id).classList.remove('active');
  document.body.style.overflow='';
}
document.querySelectorAll('.modal').forEach(m => {
  m.addEventListener('click', e => { if(e.target===m) closeModal(m.id); });
});
document.addEventListener('keydown', e => {
  if(e.key==='Escape') document.querySelectorAll('.modal.active,.lightbox.active').forEach(m=>closeModal(m.id));
});

/* ===== МОБИЛЬНОЕ МЕНЮ ===== */
const burger = document.getElementById('burger');
const mobileMenu = document.getElementById('mobileMenu');
burger.addEventListener('click', ()=> {
  mobileMenu.classList.add('active');
  burger.classList.add('open');
  burger.setAttribute('aria-expanded','true');
});
document.getElementById('menuClose').addEventListener('click', ()=> {
  mobileMenu.classList.remove('active');
  burger.classList.remove('open');
  burger.setAttribute('aria-expanded','false');
});
document.querySelectorAll('.mobile-menu a').forEach(a => {
  a.addEventListener('click', ()=> {
    mobileMenu.classList.remove('active');
    burger.classList.remove('open');
    burger.setAttribute('aria-expanded','false');
  });
});

/* ===== HEADER SCROLL ===== */
window.addEventListener('scroll', () => {
  document.getElementById('header').classList.toggle('scrolled', window.scrollY > 60);
});

/* ===== REVEAL ANIMATIONS ===== */
const observer = new IntersectionObserver(entries => {
  entries.forEach(en => { if(en.isIntersecting){ en.target.classList.add('visible'); observer.unobserve(en.target); }});
}, {threshold:0.12});
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

/* ===== ГОД ===== */
document.getElementById('year').textContent = new Date().getFullYear();
</script>
</body>
</html>