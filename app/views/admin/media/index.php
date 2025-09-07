<?php
/**
 * Vue de gestion des médias - Admin - Version moderne
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des médias - GameNews Belgium</title>
    <link rel="stylesheet" href="/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variables CSS - COULEURS DU SITE BELGIUM VIDEO GAMING */
        :root {
            /* Couleurs principales - Palette Belgique */
            --belgium-red: #FF0000;
            --belgium-yellow: #FFD700;
            --belgium-black: #000000;
            
            /* Couleurs d'accent */
            --accent-red: #FF0000;
            --accent-yellow: #FFD700;
            --accent-black: #000000;
            --accent-dark: #1a1a1a;
            --accent-gray: #2d2d2d;
            
            /* Couleurs de fond */
            --bg-primary: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            --bg-secondary: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            --bg-dark: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            
            /* Glassmorphism avec couleurs du site */
            --glass-bg: rgba(0, 0, 0, 0.8);
            --glass-bg-light: rgba(45, 45, 45, 0.9);
            --glass-bg-dark: rgba(0, 0, 0, 0.9);
            --glass-border: rgba(255, 215, 0, 0.3);
            --glass-border-dark: rgba(255, 0, 0, 0.3);
            
            /* Ombres et profondeur */
            --shadow-soft: 0 8px 32px rgba(0, 0, 0, 0.3);
            --shadow-medium: 0 12px 40px rgba(0, 0, 0, 0.4);
            --shadow-strong: 0 20px 60px rgba(0, 0, 0, 0.5);
            
            /* Transitions */
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-fast: all 0.2s ease;
            
            /* Couleurs de texte */
            --text-primary: #ffffff;
            --text-secondary: #FFD700;
            --text-light: #cccccc;
            --text-white: #ffffff;
            --text-muted: #999999;
        }

        /* Reset et base */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            margin: 0;
            color: var(--text-primary);
        }

        /* Container principal */
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header moderne */
        .admin-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
            text-align: center;
        }

        .header-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-white);
            margin: 0 0 20px 0;
            text-shadow: 0 4px 8px rgba(44, 62, 80, 0.3);
        }

        .header-actions .btn {
            background: var(--belgium-red);
            color: var(--text-white);
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
        }

        .header-actions .btn:hover {
            background: #cc0000;
            transform: translateY(-2px);
            box-shadow: var(--shadow-strong);
        }

        /* Formulaire d'upload moderne */
        .upload-form {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .upload-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--belgium-red);
        }

        .upload-form h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            color: var(--text-secondary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        /* Sélecteur de jeu moderne */
        .game-selector {
            position: relative;
        }
        
        .game-search-container {
            position: relative;
        }
        
        #filterGameDropdown {
            z-index: 1000;
        }

        .game-search {
            width: 100%;
            padding: 18px 20px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 15px;
            color: #333;
            font-size: 1rem;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
        }

        .game-search:focus {
            outline: none;
            border-color: var(--belgium-yellow);
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.1);
            transform: translateY(-1px);
        }

        .games-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: var(--shadow-strong);
            margin-top: 10px;
        }

        .game-option {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: var(--transition-smooth);
            background: white;
        }

        .game-option:hover {
            background: rgba(255, 215, 0, 0.1);
            transform: translateX(5px);
        }

        .game-option.selected {
            background: var(--belgium-red);
            color: white;
        }

        .game-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .game-title {
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        .game-details {
            color: #7f8c8d;
            font-size: 14px;
            font-weight: 400;
        }

        .game-option:hover .game-title {
            color: var(--belgium-red);
        }

        .game-option:hover .game-details {
            color: #5a6c7d;
        }



        /* Sélecteur de catégorie moderne */
        .category-selector {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .category-option {
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 25px;
            cursor: pointer;
            transition: var(--transition-smooth);
            font-size: 0.95rem;
            font-weight: 500;
            box-shadow: var(--shadow-soft);
        }

        .category-option:hover {
            background: rgba(255, 215, 0, 0.1);
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .category-option.selected {
            background: var(--belgium-red);
            color: white;
            border-color: transparent;
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        /* Zone d'upload moderne */
        .upload-area {
            border: 3px dashed rgba(255, 215, 0, 0.3);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            background: rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .upload-area:hover {
            border-color: rgba(255, 215, 0, 0.6);
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .upload-area.dragover {
            border-color: var(--belgium-yellow);
            background: rgba(255, 215, 0, 0.1);
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .upload-text {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-secondary);
        }

        .upload-hint {
            font-size: 1rem;
            color: var(--text-light);
        }

        .file-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        /* Prévisualisation moderne */
        .upload-preview {
            margin-top: 30px;
            text-align: center;
            animation: slideInUp 0.5s ease-out;
        }

        /* Filtres modernes */
        .media-filters {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
        }

        .media-filters h3 {
            color: var(--text-secondary);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-input,
        .form-select {
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: var(--transition-fast);
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--belgium-yellow);
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        }

        /* Boutons des filtres */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition-fast);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--belgium-red);
            color: var(--text-white);
        }

        .btn-primary:hover {
            background: #cc0000;
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-secondary {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
        }

        .btn-secondary:hover {
            background: #d4a700;
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        /* Grille de statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--belgium-yellow);
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Grille des médias */
        .table-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            box-shadow: var(--shadow-soft);
        }

        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .media-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            overflow: hidden;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
        }

        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
            border-color: var(--belgium-yellow);
        }

        .media-preview {
            height: 200px;
            background: var(--glass-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .media-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition-smooth);
        }

        .media-card:hover .media-image {
            transform: scale(1.05);
        }

        .media-info {
            padding: 20px;
        }

        .media-name {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-secondary);
            font-size: 1rem;
            word-break: break-word;
        }

        .media-details {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .media-actions {
            display: flex;
            gap: 10px;
        }

        .media-actions .btn {
            flex: 1;
            font-size: 0.85rem;
            padding: 8px 12px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .media-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-container {
                padding: 15px;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .preview-image {
            max-width: 250px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 15px;
            border: 3px solid rgba(102, 126, 234, 0.3);
            box-shadow: var(--shadow-soft);
            transition: var(--transition-smooth);
        }

        .preview-image:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-strong);
        }

        .upload-status {
            margin-top: 20px;
            padding: 15px 25px;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 500;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .upload-status.success {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
            box-shadow: var(--shadow-soft);
        }

        .upload-status.error {
            background: var(--belgium-red);
            color: white;
            box-shadow: var(--shadow-soft);
        }

        /* Filtres modernes */
        .media-filters {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            color: var(--belgium-yellow);
            font-size: 0.95rem;
        }

        .form-input, .form-select {
            padding: 15px 20px;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 1rem;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
        }

        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--belgium-yellow);
            box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.1);
            transform: translateY(-1px);
        }

        /* Boutons modernes */
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: var(--belgium-red);
            color: white;
        }

        .btn-primary:hover {
            background: #cc0000;
            transform: translateY(-2px);
            box-shadow: var(--shadow-strong);
        }

        .btn-secondary {
            background: var(--belgium-yellow);
            color: var(--belgium-black);
        }

        .btn-secondary:hover {
            background: #d4a700;
            transform: translateY(-2px);
            box-shadow: var(--shadow-strong);
        }

        /* Statistiques modernes */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--belgium-red);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-strong);
        }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.8;
            color: var(--belgium-yellow);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-white);
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Grille de médias moderne */
        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .media-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: var(--transition-smooth);
            position: relative;
        }

        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-strong);
        }

        /* Mode sélection */
        .media-card.selectable {
            transition: var(--transition-smooth);
        }

        .media-card.selectable:hover {
            border-color: var(--belgium-yellow);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }

        .media-card.selectable:active {
            transform: scale(0.98);
        }

        .media-preview {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .media-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition-smooth);
        }

        .media-card:hover .media-image {
            transform: scale(1.1);
        }

        .media-info {
            padding: 25px;
        }

        .media-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-white);
            line-height: 1.4;
        }

        .media-details {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            background: var(--belgium-red);
            color: white;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin: 5px 5px 5px 0;
        }

        .media-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 10px 20px;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: var(--belgium-red);
            color: white;
        }

        /* Pagination moderne */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 40px;
        }

        .page-link {
            padding: 12px 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
        }

        .page-link:hover {
            background: rgba(255, 215, 0, 0.1);
            transform: translateY(-2px);
            box-shadow: var(--shadow-strong);
        }

        .page-link.active {
            background: var(--belgium-red);
            color: white;
            border-color: transparent;
        }

        /* Loading moderne */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        }

        .loading.show {
            display: flex;
        }

        .loading-content {
            text-align: center;
            color: white;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Toast moderne */
        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 20px 30px;
            color: white;
            font-weight: 500;
            box-shadow: var(--shadow-strong);
            z-index: 10001;
            opacity: 0;
            visibility: hidden;
            transform: translateX(100%);
            transition: var(--transition-smooth);
            max-width: 400px;
        }

        .toast.show {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .media-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-container {
                padding: 15px;
            }
            
            .upload-form, .media-filters {
                padding: 25px;
            }
        }

        /* Animations d'entrée */
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .slide-up {
            animation: slideInUp 0.6s ease-out;
        }

        /* Effets de survol avancés */
        .hover-lift {
            transition: var(--transition-smooth);
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-strong);
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Header moderne -->
        <header class="admin-header">
            <div class="header-content">
                <h1><i class="fas fa-images"></i> Bibliothèque de Médias</h1>
                <div class="header-actions">
                    <a href="/admin.php" class="btn">
                        <i class="fas fa-arrow-left"></i>
                        Retour au tableau de bord
                    </a>
                </div>
            </div>
        </header>

        <!-- Formulaire d'upload moderne -->
        <div class="upload-form">
            <h3><i class="fas fa-cloud-upload-alt"></i> Upload de média</h3>
            
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="form-row">
                    <!-- Sélection de jeu -->
                    <div class="form-group">
                        <label for="gameSearch" class="form-label">
                            <i class="fas fa-gamepad"></i> Jeu associé (optionnel)
                        </label>
                        <div class="game-selector">
                            <input type="text" id="gameSearch" class="game-search" 
                                   placeholder="Rechercher un jeu..." autocomplete="off">
                            <input type="hidden" id="gameId" name="game_id" value="">
                            <div class="games-dropdown" id="gamesDropdown"></div>
                        </div>
                        <div class="form-help">Laissez vide pour un média général</div>
                    </div>
                    
                    <!-- Catégorie -->
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-folder"></i> Catégorie
                        </label>
                        <div class="category-selector">
                            <div class="category-option selected" data-category="screenshots">
                                <i class="fas fa-camera"></i> Screenshots
                            </div>
                            <div class="category-option" data-category="news">
                                <i class="fas fa-newspaper"></i> News
                            </div>
                            <div class="category-option" data-category="events">
                                <i class="fas fa-calendar-alt"></i> Événements
                            </div>
                            <div class="category-option" data-category="other">
                                <i class="fas fa-ellipsis-h"></i> Autre
                            </div>
                        </div>
                        <input type="hidden" id="category" name="category" value="screenshots">
                    </div>
                </div>
                
                <!-- Zone d'upload moderne -->
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-text">Glissez-déposez vos fichiers ici</div>
                    <div class="upload-hint">ou cliquez pour sélectionner des fichiers</div>
                    <input type="file" id="fileInput" class="file-input" accept="image/*" multiple>
                </div>
                
                <!-- Prévisualisation moderne -->
                <div class="upload-preview" id="uploadPreview" style="display: none;">
                    <img id="previewImage" class="preview-image" alt="Aperçu">
                    <div class="upload-status" id="uploadStatus"></div>
                </div>
                
                <div class="upload-hint" style="text-align: center; margin-top: 20px; color: #666;">
                    <i class="fas fa-info-circle"></i>
                    Formats acceptés : JPG, PNG, WebP, GIF • Taille maximale : 4MB par fichier
                </div>
            </form>
        </div>

        <!-- Filtres modernes -->
        <div class="media-filters">
            <h3><i class="fas fa-filter"></i> Filtres et recherche</h3>
            <div class="filter-row">
                <div class="form-group">
                    <label for="filterSearch" class="form-label">
                        <i class="fas fa-search"></i> Rechercher
                    </label>
                    <input type="text" id="filterSearch" class="form-input" placeholder="Nom du fichier...">
                </div>
                <div class="form-group">
                    <label for="filterGame" class="form-label">
                        <i class="fas fa-gamepad"></i> Jeu
                    </label>
                    <div class="game-search-container">
                        <input type="text" id="filterGame" class="form-input" placeholder="Rechercher un jeu..." autocomplete="off">
                        <div id="filterGameDropdown" class="games-dropdown"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="filterCategory" class="form-label">
                        <i class="fas fa-folder"></i> Catégorie
                    </label>
                    <select id="filterCategory" class="form-select">
                        <option value="">Toutes les catégories</option>
                        <option value="screenshots">Screenshots</option>
                        <option value="news">News</option>
                        <option value="events">Événements</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="button" id="applyFilters" class="btn btn-primary">
                        <i class="fas fa-search"></i> Appliquer
                    </button>
                    <button type="button" id="resetFilters" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistiques modernes -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div class="stat-number"><?= $totalMedias ?></div>
                <div class="stat-label">Total des médias</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <div class="stat-number"><?= count($games) ?></div>
                <div class="stat-label">Jeux disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-folder"></i>
                </div>
                <div class="stat-number"><?= $currentPage ?></div>
                <div class="stat-label">Page actuelle</div>
            </div>
        </div>

        <!-- Liste des médias moderne -->
        <div class="table-container">
            <h3 style="color: var(--text-secondary); margin-bottom: 25px; font-size: 1.8rem;">
                <i class="fas fa-list"></i> 
                <?php if ($selectMode): ?>
                    Sélectionner une image
                <?php else: ?>
                    Bibliothèque des médias
                <?php endif; ?>
            </h3>
            
            <?php if ($selectMode): ?>
                <div class="select-mode-info" style="background: var(--glass-bg); padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid var(--glass-border);">
                    <i class="fas fa-info-circle" style="color: var(--accent-blue); margin-right: 10px;"></i>
                    <strong>Mode sélection :</strong> Cliquez sur une image pour la sélectionner comme couverture de jeu.
                </div>
            <?php endif; ?>
            <div class="media-grid" id="mediaGrid">
                <?php foreach ($medias as $media): ?>
                <div class="media-card <?= $selectMode ? 'selectable' : '' ?>" 
                     data-id="<?= $media->getId() ?>" 
                     data-game="<?= $media->getGameId() ?>" 
                     data-category="<?= $media->getMediaType() ?>"
                     <?php if ($selectMode && $media->isImage()): ?>
                     onclick="selectMedia(<?= $media->getId() ?>, '<?= htmlspecialchars($media->getOriginalName()) ?>')"
                     style="cursor: pointer;"
                     <?php endif; ?>>
                    <div class="media-preview">
                        <?php if ($media->isImage()): ?>
                            <img src="<?= $media->getUrl() ?>" 
                                 alt="<?= htmlspecialchars($media->getOriginalName()) ?>" 
                                 class="media-image">
                        <?php else: ?>
                            <div class="icon" style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 3rem; opacity: 0.5;">
                                <i class="fas fa-video"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="media-info">
                        <div class="media-name"><?= htmlspecialchars($media->getOriginalName()) ?></div>
                        <div class="media-details">
                            <i class="fas fa-file"></i> <?= $media->getFormattedSize() ?> • <?= $media->getMimeType() ?><br>
                            <?php if ($media->getGameId()): ?>
                                <span class="badge"><i class="fas fa-gamepad"></i> Jeu associé</span><br>
                            <?php endif; ?>
                            <i class="fas fa-clock"></i> Ajouté le <?= date('d/m/Y H:i', strtotime($media->getCreatedAt())) ?>
                        </div>
                        <?php if (!$selectMode): ?>
                        <div class="media-actions">
                            <button class="btn btn-sm btn-primary" onclick="copyUrl('<?= $media->getUrl() ?>')">
                                <i class="fas fa-copy"></i> Copier URL
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteMedia(<?= $media->getId() ?>)">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="media-actions">
                            <?php if ($media->isImage()): ?>
                                <button class="btn btn-sm btn-success" onclick="selectMedia(<?= $media->getId() ?>, '<?= htmlspecialchars($media->getOriginalName()) ?>')">
                                    <i class="fas fa-check"></i> Sélectionner
                                </button>
                            <?php else: ?>
                                <span class="text-muted">Vidéo non sélectionnable</span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pagination moderne -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Précédent
                </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <a href="?page=<?= $i ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="page-link">
                    Suivant <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Loading moderne -->
    <div class="loading" id="loading">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="upload-text">Upload en cours...</div>
        </div>
    </div>

    <!-- Toast moderne -->
    <div class="toast" id="toast"></div>

    <script>
        // Variables globales
        const csrfToken = '<?= Auth::generateCsrfToken() ?>';
        let selectedGame = null;
        let selectedCategory = 'screenshots';
        
        // Éléments DOM
        const uploadForm = document.getElementById('uploadForm');
        const gameSearch = document.getElementById('gameSearch');
        const gameId = document.getElementById('gameId');
        const gamesDropdown = document.getElementById('gamesDropdown');
        const categoryOptions = document.querySelectorAll('.category-option');
        const categoryInput = document.getElementById('category');
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadPreview = document.getElementById('uploadPreview');
        const previewImage = document.getElementById('previewImage');
        const uploadStatus = document.getElementById('uploadStatus');
        const mediaGrid = document.getElementById('mediaGrid');
        const loading = document.getElementById('loading');
        const toast = document.getElementById('toast');
        
        // Gestion de la recherche de jeux
        let searchTimeout;
        gameSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                gamesDropdown.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(() => {
                searchGames(query);
            }, 300);
        });
        
        // Utiliser les jeux déjà chargés en PHP (disponible globalement)
        const allGames = <?= json_encode(array_map(function($game) {
            try {
                $hardwareName = $game->getHardwareName();
                return [
                    'id' => $game->getId(),
                    'title' => $game->getTitle(),
                    'slug' => $game->getSlug(),
                    'hardware' => $hardwareName ?: 'Aucun hardware'
                ];
            } catch (Exception $e) {
                return [
                    'id' => $game->getId(),
                    'title' => $game->getTitle(),
                    'slug' => $game->getSlug(),
                    'hardware' => 'Aucun hardware'
                ];
            }
        }, $games)) ?>;
        
        // Recherche de jeux (utilise les données PHP déjà chargées)
        function searchGames(query) {
            console.log('🔍 Recherche de jeux pour:', query);
            
            // Filtrer les jeux localement
            const filteredGames = allGames.filter(game => 
                game.title.toLowerCase().includes(query.toLowerCase())
            ).slice(0, 10); // Limiter à 10 résultats
            
            console.log('✅ Jeux trouvés:', filteredGames);
            displayGamesDropdown(filteredGames);
        }
        
        // Gestion de la recherche de jeux pour le filtre
        const filterGameInput = document.getElementById('filterGame');
        const filterGameDropdown = document.getElementById('filterGameDropdown');
        let filterGameTimeout;
        let selectedFilterGame = null;
        
        filterGameInput.addEventListener('input', function() {
            clearTimeout(filterGameTimeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                filterGameDropdown.style.display = 'none';
                selectedFilterGame = null;
                return;
            }
            
            filterGameTimeout = setTimeout(() => {
                searchFilterGames(query);
            }, 300);
        });
        
        // Recherche de jeux pour le filtre (utilise les mêmes données)
        function searchFilterGames(query) {
            console.log('🔍 Recherche de jeux pour filtre:', query);
            
            // Filtrer les jeux localement
            const filteredGames = allGames.filter(game => 
                game.title.toLowerCase().includes(query.toLowerCase())
            ).slice(0, 10);
            
            console.log('✅ Jeux trouvés pour filtre:', filteredGames);
            displayFilterGamesDropdown(filteredGames);
        }
        
        // Afficher le dropdown des jeux pour le filtre
        function displayFilterGamesDropdown(games) {
            filterGameDropdown.innerHTML = '';
            
            if (games.length === 0) {
                filterGameDropdown.innerHTML = '<div class="game-option"><i class="fas fa-info-circle"></i> Aucun jeu trouvé</div>';
            } else {
                games.forEach(game => {
                    const option = document.createElement('div');
                    option.className = 'game-option';
                    option.innerHTML = `
                        <div class="game-info">
                            <div class="game-title">${game.title}</div>
                            <div class="game-details">${game.hardware || 'Aucun hardware'}</div>
                        </div>
                    `;
                    option.addEventListener('click', () => selectFilterGame(game));
                    filterGameDropdown.appendChild(option);
                });
            }
            
            filterGameDropdown.style.display = 'block';
        }
        
        // Sélectionner un jeu pour le filtre
        function selectFilterGame(game) {
            selectedFilterGame = game;
            filterGameInput.value = game.title;
            filterGameDropdown.style.display = 'none';
            console.log('🎮 Jeu sélectionné pour filtre:', game);
        }
        
        // Masquer le dropdown quand on clique ailleurs
        document.addEventListener('click', function(e) {
            if (!filterGameInput.contains(e.target) && !filterGameDropdown.contains(e.target)) {
                filterGameDropdown.style.display = 'none';
            }
        });
        
        // Afficher le dropdown des jeux
        function displayGamesDropdown(games) {
            gamesDropdown.innerHTML = '';
            
            if (games.length === 0) {
                gamesDropdown.innerHTML = '<div class="game-option"><i class="fas fa-info-circle"></i> Aucun jeu trouvé</div>';
            } else {
                games.forEach(game => {
                    const option = document.createElement('div');
                    option.className = 'game-option';
                    option.innerHTML = `
                        <div class="game-info">
                            <div class="game-title">${game.title}</div>
                            <div class="game-details">${game.hardware || 'Aucun hardware'}</div>
                        </div>
                    `;
                    option.addEventListener('click', () => selectGame(game));
                    gamesDropdown.appendChild(option);
                });
            }
            
            gamesDropdown.style.display = 'block';
        }
        
        // Sélectionner un jeu
        function selectGame(game) {
            selectedGame = game;
            gameSearch.value = game.title;
            gameId.value = game.id;
            gamesDropdown.style.display = 'none';
            
            showToast(`🎮 Jeu sélectionné : ${game.title}`, 'success');
        }
        
        // Gestion des catégories
        categoryOptions.forEach(option => {
            option.addEventListener('click', function() {
                categoryOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                selectedCategory = this.dataset.category;
                categoryInput.value = selectedCategory;
            });
        });
        
        // Gestion du drag & drop
        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });
        
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
        
        // Gestion des fichiers
        function handleFiles(files) {
            Array.from(files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    showPreview(file);
                    uploadFile(file);
                } else {
                    showToast('❌ Format de fichier non supporté', 'error');
                }
            });
        }
        
        // Afficher la prévisualisation
        function showPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                uploadPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
        
        // Upload d'un fichier
        async function uploadFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('csrf_token', csrfToken);
            formData.append('game_id', gameId.value);
            formData.append('category', categoryInput.value);
            
            loading.classList.add('show');
            uploadStatus.textContent = 'Upload en cours...';
            uploadStatus.className = 'upload-status';
            
            try {
                const response = await fetch('/api_media_upload.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    uploadStatus.textContent = '✅ Upload réussi !';
                    uploadStatus.className = 'upload-status success';
                    showToast('🎉 Fichier uploadé avec succès !', 'success');
                    
                    // Recharger la page après un délai
                    setTimeout(() => location.reload(), 1500);
                } else {
                    uploadStatus.textContent = result.error || '❌ Erreur lors de l\'upload';
                    uploadStatus.className = 'upload-status error';
                    showToast(result.error || '❌ Erreur lors de l\'upload', 'error');
                }
            } catch (error) {
                uploadStatus.textContent = '❌ Erreur de connexion';
                uploadStatus.className = 'upload-status error';
                showToast('❌ Erreur de connexion', 'error');
            } finally {
                loading.classList.remove('show');
            }
        }
        
        // Supprimer un média
        async function deleteMedia(id) {
            if (!confirm('⚠️ Êtes-vous sûr de vouloir supprimer ce média ?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', csrfToken);
            formData.append('id', id);
            
            try {
                const response = await fetch('/api_media_delete.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('🗑️ Média supprimé avec succès !', 'success');
                    document.querySelector(`[data-id="${id}"]`).remove();
                } else {
                    showToast(result.error || '❌ Erreur lors de la suppression', 'error');
                }
            } catch (error) {
                showToast('❌ Erreur de connexion', 'error');
            }
        }
        
        // Copier l'URL d'un média
        function copyUrl(url) {
            navigator.clipboard.writeText(url).then(() => {
                showToast('📋 URL copiée dans le presse-papiers !', 'success');
            }).catch(() => {
                showToast('❌ Erreur lors de la copie', 'error');
            });
        }
        
        // Filtres
        document.getElementById('applyFilters').addEventListener('click', applyFilters);
        document.getElementById('resetFilters').addEventListener('click', resetFilters);
        
        function applyFilters() {
            const search = document.getElementById('filterSearch').value.toLowerCase();
            const game = selectedFilterGame ? selectedFilterGame.id : '';
            const category = document.getElementById('filterCategory').value;
            
            console.log('🔍 Filtres appliqués:', { search, game, category });
            console.log('🎮 Jeu sélectionné:', selectedFilterGame);
            
            const mediaCards = document.querySelectorAll('.media-card');
            console.log('📁 Nombre de cartes médias:', mediaCards.length);
            
            mediaCards.forEach((card, index) => {
                const name = card.querySelector('.media-name').textContent.toLowerCase();
                const cardGame = card.dataset.game;
                const cardCategory = card.dataset.category;
                
                console.log(`📄 Carte ${index + 1}:`, { name, cardGame, cardCategory });
                
                let show = true;
                
                if (search && !name.includes(search)) show = false;
                if (game && cardGame !== game.toString()) show = false;
                if (category && cardCategory !== category) show = false;
                
                console.log(`✅ Carte ${index + 1} affichée:`, show);
                card.style.display = show ? 'block' : 'none';
            });
        }
        
        function resetFilters() {
            document.getElementById('filterSearch').value = '';
            document.getElementById('filterGame').value = '';
            document.getElementById('filterCategory').value = '';
            selectedFilterGame = null;
            filterGameDropdown.style.display = 'none';
            
            const mediaCards = document.querySelectorAll('.media-card');
            mediaCards.forEach(card => card.style.display = 'block');
        }
        
        // Afficher un toast moderne
        function showToast(message, type = 'success') {
            toast.textContent = message;
            
            // Ajouter des icônes selon le type
            let icon = '';
            switch(type) {
                case 'success':
                    icon = '✅ ';
                    toast.style.background = 'var(--belgium-yellow)';
                    toast.style.color = 'var(--belgium-black)';
                    break;
                case 'error':
                    icon = '❌ ';
                    toast.style.background = 'var(--belgium-red)';
                    toast.style.color = 'white';
                    break;
                case 'warning':
                    icon = '⚠️ ';
                    toast.style.background = 'var(--belgium-yellow)';
                    toast.style.color = 'var(--belgium-black)';
                    break;
                default:
                    icon = 'ℹ️ ';
                    toast.style.background = 'var(--belgium-red)';
                    toast.style.color = 'white';
            }
            
            toast.textContent = icon + message;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }
        
        // Fermer le dropdown en cliquant ailleurs
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.game-selector')) {
                gamesDropdown.style.display = 'none';
            }
        });

        // Animation d'entrée des éléments
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.stat-card, .media-card, .upload-form, .media-filters');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('fade-in');
                }, index * 100);
            });
        });

        // Fonction de sélection d'image (mode sélection)
        function selectMedia(mediaId, mediaName) {
            // Vérifier si on est en mode sélection
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('select_mode') !== '1') {
                return;
            }

            // Envoyer les données à la fenêtre parent
            const mediaData = {
                id: mediaId,
                original_name: mediaName
            };

            // Envoyer le message à la fenêtre parent
            if (window.opener) {
                window.opener.postMessage({
                    type: 'mediaSelected',
                    media: mediaData
                }, window.location.origin);
            }

            // Afficher un message de confirmation
            showToast(`Image "${mediaName}" sélectionnée !`, 'success');
            
            // Fermer la fenêtre après un court délai
            setTimeout(() => {
                window.close();
            }, 1000);
        }
    </script>
</body>
</html>
