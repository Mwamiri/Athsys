# 👨‍💻 Developer Information & Support

## Developer Contact

**Organization:** Mwamiri Computers  
**Email:** support@mwamiri.co.ke  
**Website:** https://mwamiri.co.ke  
**Project Website:** https://mwamiri.co.ke/athsys  
**Update Server:** https://mwamiri.co.ke/athsys/api

---

## 📱 Contact Methods

### Primary Support Channel
**Email:** support@mwamiri.co.ke
- Response time: 24 hours
- Support hours: Business days
- Languages: English, Swahili

### Website
Visit: https://mwamiri.co.ke/athsys
- Documentation
- Download updates
- Report issues
- Feature requests

### Emergency Support
For critical system failures:
- Mark email subject: `[CRITICAL]`
- Include system version and error details
- Provide access credentials (if safe)

---

## 🐛 Bug Reporting

### How to Report a Bug

1. **Gather Information:**
   - System version (check status.php)
   - PHP version
   - Database type and version
   - Steps to reproduce

2. **Check Error Logs:**
   - Visit: `status.php` → Check diagnostics
   - Review: `install/error.log`
   - Check PHP error logs on server

3. **Email Report to:**
   support@mwamiri.co.ke

4. **Include in Email:**
   ```
   Subject: [BUG] Brief description
   
   System Version: 1.0.0
   PHP Version: 8.1
   Database: MySQL 8.0
   
   Steps to reproduce:
   1. ...
   2. ...
   3. ...
   
   Expected behavior: ...
   Actual behavior: ...
   
   Error message (if any): ...
   ```

---

## 💡 Feature Requests

### Requesting a New Feature

**Email:** support@mwamiri.co.ke  
**Subject:** `[FEATURE REQUEST] Your feature name`

Include:
- Clear description of feature
- Use cases
- Expected behavior
- Estimated priority (Low/Medium/High)
- Any design mockups or examples

---

## 📊 System Diagnostics

### Run Self-Diagnostic
Visit: `http://your-domain.com/status.php`

This will show:
- Installation status
- Database connectivity
- PHP requirements
- File permissions
- System health

### Access Error Logs
```bash
# Via web browser
http://domain.com/install/error.log

# Via CLI
cat /install/error.log

# View latest 50 errors
php install/helper.php logs
```

### Generate Diagnostic Report
```bash
php install/helper.php requirements
# Shows all system requirements and status
```

---

## 🔄 Update Information

### Checking for Updates
Visit: `http://your-domain.com/update.php`

Or via CLI:
```bash
php install/helper.php check-updates
```

### Update Sources
- **Stable Updates:** https://mwamiri.co.ke/athsys/api/check
- **Beta Updates:** Contact support for beta access
- **Security Patches:** Automatic notification

### Update History
Check file: `/install/updates.log`

---

## 📚 Documentation

### Available Documentation

| Document | URL | Content |
|----------|-----|---------|
| Installation Guide | INSTALLATION-GUIDE.md | Complete setup guide |
| System Overview | INSTALLATION-SYSTEM.md | Technical documentation |
| Quick Start | QUICK-START.md | 30-second setup |
| Improvements | SYSTEM-IMPROVEMENTS.md | Enhancement roadmap |
| This File | DEVELOPER.md | Developer information |

### Generating Documentation
```bash
# View all help
ls *.md

# View specific guide
cat INSTALLATION-GUIDE.md
```

---

## 🛠️ Development Setup

### Prerequisites
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+
- Apache with mod_rewrite
- 50MB disk space

### Local Installation
1. Clone/download files
2. Run `installer.php` in browser
3. Follow setup wizard
4. Access dashboard

### Development Environment
```bash
# Check requirements
php install/helper.php requirements

# View system info
php install/helper.php diagnostics

# Clear cache (if needed)
php install/helper.php clear-cache
```

---

## 🔐 Security Reporting

### Report Security Issues

**IMPORTANT:** Do NOT post security vulnerabilities publicly.

**Email:** security@mwamiri.co.ke  
**Subject:** `[SECURITY] Vulnerability Report`

Include:
- Description of vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if available)

**Disclosure:** We follow responsible disclosure (90 days)

---

## 🤝 Contributing

### Contributing Code

1. **Fork/Download**
   - Get latest version

2. **Create Feature Branch**
   ```bash
   git checkout -b feature/your-feature
   ```

3. **Write Code**
   - Follow code style
   - Add comments
   - Test thoroughly

4. **Send Pull Request**
   - Include description
   - Reference any issues
   - Wait for review

### Code Style Guidelines

```php
<?php
// Use proper namespacing
namespace AthletesResultsSystem;

// Use meaningful variable names
$userAuthenticated = true;

// Include PHPDoc comments
/**
 * Function description
 * @param type $param Description
 * @return type Description
 */
public function functionName($param) {
    // Code here
}

// Proper error handling
try {
    // Code that might fail
} catch (Exception $e) {
    SystemFailsafe::log($e->getMessage(), 'ERROR');
}

// Use PDO for database queries
$statement = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$statement->execute([$userId]);
?>
```

---

## 📞 Support Levels

### Free Support
- General usage questions
- Installation help
- Basic troubleshooting
- Documentation guidance
- Response time: 48 hours

### Priority Support (Optional)
- Faster response times (4 hours)
- Direct phone support
- Custom implementations
- Dedicated contact person

Contact: support@mwamiri.co.ke for details

---

## 🎓 Training & Onboarding

### Available Training

1. **Installation Training**
   - Step-by-step installation
   - Configuration guide
   - Best practices
   - Duration: 30 minutes

2. **Administration Training**
   - User management
   - System configuration
   - Backup procedures
   - Duration: 1 hour

3. **Developer Training**
   - Code structure
   - Database schema
   - Custom development
   - Duration: 2 hours

Contact: support@mwamiri.co.ke

---

## 📋 Maintenance & Support SLA

### Standard SLA
- **Availability:** 99.5% uptime
- **Response Time:** 24 hours
- **Resolution Time:** 72 hours
- **Critical Issues:** 4 hours

### Service Included
- Regular updates
- Security patches
- Bug fixes
- Documentation updates

---

## 🔍 Quality Assurance

### Testing Standards
- Unit tests
- Integration tests
- Performance tests
- Security audits

### Quality Metrics
- Code coverage: > 80%
- Automated tests: 100%
- Security scan: Passed
- Performance: Optimized

---

## 📈 Roadmap

### Q1 2026
- [ ] Mobile app release
- [ ] Advanced analytics
- [ ] Performance optimization

### Q2 2026
- [ ] API marketplace
- [ ] Third-party integrations
- [ ] Enterprise features

### Q3 2026
- [ ] AI-powered insights
- [ ] Predictive analytics
- [ ] Automated reports

---

## 💰 Pricing & Licensing

### Version 1.0
**License:** Community License  
**Cost:** Free  
**Support:** Community-based

### Professional Support
**Email:** support@mwamiri.co.ke  
**Options Available:**
- Priority support
- Custom development
- Training & onboarding
- Dedicated hosting

---

## 📝 License Information

This software is provided as-is for non-commercial educational use.

**Developed by:** Mwamiri Computers  
**Year:** 2025

For full license terms, contact: support@mwamiri.co.ke

---

## 🎯 Getting Help Quickly

### Quick Troubleshooting

**Installation won't start?**
- Clear browser cache
- Try different browser
- Check file permissions

**Database connection failed?**
- Verify credentials
- Test MySQL manually
- Check firewall rules

**Getting slow performance?**
- Check server resources
- Review database indexes
- Clear cache

**Still stuck?**
- Visit: status.php
- Check: install/error.log
- Email: support@mwamiri.co.ke

---

## 📞 Emergency Contacts

| Issue | Contact | Priority |
|-------|---------|----------|
| General Questions | support@mwamiri.co.ke | Normal |
| Bug Reports | support@mwamiri.co.ke | High |
| Security Issues | security@mwamiri.co.ke | Critical |
| Sales Inquiries | sales@mwamiri.co.ke | Normal |

---

## 🌐 Online Resources

- **Website:** https://mwamiri.co.ke
- **Project:** https://mwamiri.co.ke/athsys
- **Documentation:** https://mwamiri.co.ke/athsys/docs
- **Updates:** https://mwamiri.co.ke/athsys/updates
- **Support:** support@mwamiri.co.ke

---

**Last Updated:** November 11, 2025  
**Version:** 1.0  
**Developed by:** Mwamiri Computers

For the latest information, visit: https://mwamiri.co.ke/athsys
