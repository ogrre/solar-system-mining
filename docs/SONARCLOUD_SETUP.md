# SonarCloud Setup Guide

## Configuration Required

### 1. SonarCloud Project Settings
- Go to your project on [SonarCloud.io](https://sonarcloud.io)
- Navigate to **Administration** → **Analysis Method**
- **Disable "Automatic Analysis"** 
- **Enable "CI-based analysis"** instead

### 2. GitHub Repository Secrets
Ensure these secrets are configured in GitHub Settings → Secrets and variables → Actions:
- `SONAR_TOKEN`: Token from SonarCloud (Project → Information → Tokens)

### 3. Project Configuration
The following files configure SonarCloud analysis:
- `sonar-project.properties`: Project settings and exclusions
- `.github/workflows/laravel.yml`: CI pipeline with SonarCloud scan

## Troubleshooting

### "Automatic Analysis is enabled" Error
**Problem**: CI analysis conflicts with Automatic Analysis
**Solution**: Disable Automatic Analysis in SonarCloud project settings

### Action Deprecated Warning  
**Problem**: Using deprecated `sonarcloud-github-action`
**Solution**: ✅ Updated to use `sonarqube-scan-action@v5.0.0`

### Coverage Not Found
**Problem**: SonarCloud can't find coverage.xml
**Solution**: ✅ Tests run with `--coverage-clover coverage.xml` before SonarCloud scan

### Missing test-results.xml File
**Problem**: SonarCloud can't import PHPUnit test results
**Solution**: ✅ Added `--log-junit test-results.xml` generation alongside coverage

### PostgreSQL Role Error in CI
**Problem**: "FATAL: role 'root' does not exist" during tests
**Solution**: ✅ Added explicit PostgreSQL environment variables for test execution

## Quality Gates
The project is configured to:
- ✅ Require SonarCloud analysis to pass
- ✅ Require Laravel tests to pass  
- ✅ Block merge if quality gate fails