# Overview

This is a Progressive Web Application (PWA) for "بازار افغانستان" (Afghanistan Market), an online marketplace platform designed for buying and selling real estate, vehicles, electronics, and other goods in Afghanistan. The application supports multiple languages (Dari/Persian, Pashto, and English) with RTL/LTR text direction capabilities, targeting the Afghan market with culturally appropriate design and functionality.

# User Preferences

Preferred communication style: Simple, everyday language.

# System Architecture

## Frontend Architecture
- **Static HTML/CSS/JavaScript**: Pure client-side web application with no backend framework dependencies
- **Multi-language Support**: Built-in internationalization with RTL (right-to-left) text direction for Persian/Dari and Pashto, and LTR for English
- **Responsive Design**: Mobile-first approach with viewport meta tags and responsive CSS
- **Progressive Web App**: Implements PWA standards with manifest.json for offline capabilities and native app-like experience

## Design System
- **Visual Theme**: Dark gradient background (purple to blue tones) with glassmorphism effects using backdrop-filter and transparent overlays
- **Typography**: Tahoma font family as primary, with Afghan Sans fallback for Pashto text
- **Component Architecture**: Modular CSS with reusable `.section-box` components for consistent UI elements
- **Color Scheme**: Dark theme with purple gradient (#0f0c29 to #302b63 to #24243e) and white text overlays

## Page Structure
- **Homepage (index.html)**: Main landing page with marketplace overview and navigation
- **Authentication (login.html)**: Dedicated login/registration page with form-based user authentication
- **Modular Layout**: Each page follows consistent structure with shared styling and responsive design patterns

## Authentication System
- **Form-based Authentication**: Traditional login/registration forms with client-side validation
- **Session Management**: Designed for integration with backend authentication services
- **User Experience**: Centered modal-style login forms with glassmorphism design

## Internationalization
- **Multi-script Support**: Handles Arabic/Persian script (RTL) and Latin script (LTR)
- **Language Switching**: Dynamic direction and font family changes based on language selection
- **Cultural Adaptation**: Afghan-specific design elements and market terminology

# External Dependencies

## Browser APIs
- **PWA APIs**: Service Worker API, Web App Manifest for offline functionality and app installation
- **Responsive Design**: CSS3 features including backdrop-filter, gradients, and flexbox

## Font Dependencies
- **Primary Font**: Tahoma (system font, widely available)
- **Pashto Font**: Afghan Sans (fallback font for Pashto language support)

## No Current Backend
- **Static Hosting**: Application is currently frontend-only, designed for static hosting
- **Future Backend Integration**: Architecture prepared for REST API integration for user authentication, product listings, and marketplace functionality

## PWA Features
- **Offline Capability**: Manifest.json configured for standalone app experience
- **App Icons**: SVG-based icons for various device sizes (192x192, 512x512)
- **Theme Integration**: Native mobile app appearance with custom theme colors and status bar styling