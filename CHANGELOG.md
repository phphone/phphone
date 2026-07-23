# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- GitHub Actions pipeline for automated builds.

## [v1.0.0] - 2026-07-22
### Added
- **Phphone CLI**: Intelligent global command-line tool for project management (`create`, `run`, `build`, `setup`, `config`, etc).
- **Android Support**: Full integration with JNI, embedding a lightweight PHP 8.4 engine running in C++.
- **iOS Support**: Full integration with XcodeGen and Swift bridges to run PHP 8.4 natively.
- **Hot Reload**: Automatic live reloading when PHP/JS/CSS files change (`phphone run`).
- **Setup Command**: Automated generation of App Icons and Splash Screens (`phphone setup`).
- **Config Command**: Native configuration handling (Orientation lock, Pinch-to-zoom prevention).
- **Design Guides**: Implemented UI/UX best practices in documentation (Safe Area padding, Overscroll prevention).

### Changed
- Refactored core engine to intercept all network traffic locally, removing CORS issues.
- Migrated Gradle scripts to Kotlin DSL (`build.gradle.kts`) for modern dependency management.

