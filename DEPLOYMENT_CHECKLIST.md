# 🚀 Deployment Checklist - etectstore

**Project:** etectstore E-commerce  
**Version:** 1.0.0  
**Date:** 22 Desember 2025

---

## Pre-Deployment Checklist

### 1. Code Quality ✅
- [x] All bugs fixed
- [x] Security vulnerabilities patched
- [x] CSRF protection implemented
- [x] Input validation added
- [x] Error handling improved
- [x] Code reviewed
- [x] Documentation updated

### 2. Database ⏳
- [ ] Database backup created
- [ ] Run `database/optimize.sql`
- [ ] Verify indexes created
- [ ] Test database connection
- [ ] Update `config/db.php` credentials
- [ ] Secure database password

### 3. File System ⏳
- [ ] Create `cache/` directory
- [ ] Create `logs/` directory
- [ ] Set proper file permissions (755/644)
- [ ] Verify `public/assets/images/` writable
- [ ] Remove test/debug files
- [ ] Remove `.git` if deploying via FTP

### 4. Configuration ⏳
- [ ] Update database credentials in `config/db.php`
- [ ] Verify base URLs are correct
- [ ] Configure email settings (if applicable)
- [ ] Set timezone in `php.ini`
- [ ] Disable `display_errors` in production
- [ ] Enable `log_errors` in production

### 5. Security ⏳
- [ ] SSL certificate installed
- [ ] Force HTTPS in `.htaccess`
- [ ] Secure `config/` directory
- [ ] Set secure session cookies
- [ ] Review file upload directory permissions
- [ ] Change default admin password
- [ ] Remove test accounts

### 6. Performance ⏳
- [ ] Enable Gzip compression
- [ ] Configure browser caching
- [ ] Optimize images
- [ ] Minify CSS/JS (optional)
- [ ] Test page load speed
- [ ] Configure PHP OPcache

### 7. Testing ⏳
- [ ] Test all forms with CSRF protection
- [ ] Test file upload functionality
- [ ] Test checkout process
- [ ] Test payment flow (if integrated)
- [ ] Test on different browsers
- [ ] Test on mobile devices
- [ ] Load testing (optional)

---

## Deployment Steps

### Step 1: Backup Current System
```bash
# Backup database
mysqldump -u root -p etectstore > backup_$(date +%Y%m%d).sql

# Backup files
tar -czf backup_files_$(date +%Y%m%d).tar.gz /path/to/etectstore
```

### Step 2: Upload Files
```bash
# Via FTP/SFTP
# Upload all files except:
# - .git/
# - cache/
# - logs/
# - *.md (documentation, optional)

# OR via Git
git pull origin main
```

### Step 3: Set Permissions
```bash
# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set special permissions
chmod 777 cache/
chmod 777 public/assets/images/
chmod 600 config/db.php
```

### Step 4: Database Setup
```bash
# Import database
mysql -u your_user -p your_database < database/etectstore.sql

# Run optimization
mysql -u your_user -p your_database < database/optimize.sql

# Verify
mysql -u your_user -p your_database -e "SHOW TABLES;"
```

### Step 5: Configuration
Edit `config/db.php`:
```php
$host = 'localhost';
$db   = 'production_db_name';
$user = 'production_user';
$pass = 'secure_password_here';
```

### Step 6: Test
```bash
# Test database connection
curl http://yoursite.com/test_connection.php

# Test main pages
curl http://yoursite.com/
curl http://yoursite.com/auth/login.php
curl http://yoursite.com/pages/products.php
```

---

## Post-Deployment Checklist

### Immediate Testing (First 30 minutes)
- [ ] Homepage loads correctly
- [ ] Login works
- [ ] Registration works
- [ ] Product listing loads
- [ ] Product detail page works
- [ ] Add to cart works
- [ ] Checkout process works
- [ ] Admin panel accessible
- [ ] No PHP errors in logs
- [ ] HTTPS working (if enabled)

### Day 1 Monitoring
- [ ] Check error logs: `tail -f logs/error.log`
- [ ] Check PHP errors: `tail -f /var/log/php_errors.log`
- [ ] Monitor server resources (CPU, RAM, Disk)
- [ ] Test user registration flow
- [ ] Test order placement
- [ ] Verify email notifications (if configured)

### Week 1 Monitoring
- [ ] Review error logs daily
- [ ] Monitor database performance
- [ ] Check slow query log
- [ ] Verify backup cron jobs
- [ ] Test all features again
- [ ] Collect user feedback

---

## Rollback Plan

If something goes wrong:

### Immediate Rollback (< 15 minutes)
```bash
# 1. Restore database
mysql -u root -p etectstore < backup_YYYYMMDD.sql

# 2. Restore files
tar -xzf backup_files_YYYYMMDD.tar.gz -C /var/www/

# 3. Clear cache
rm -rf cache/*

# 4. Restart web server
sudo systemctl restart apache2
# OR
sudo systemctl restart nginx
```

### Investigate Issues
```bash
# Check error logs
tail -100 logs/error.log
tail -100 /var/log/apache2/error.log

# Check PHP errors
tail -100 /var/log/php_errors.log

# Check database
mysql -u root -p
USE etectstore;
SHOW PROCESSLIST;
SHOW STATUS;
```

---

## Monitoring & Maintenance

### Daily Tasks
- [ ] Check error logs
- [ ] Monitor server resources
- [ ] Verify backup completed

### Weekly Tasks
- [ ] Review slow queries
- [ ] Optimize database tables
- [ ] Clear old cache files
- [ ] Update dependencies (if applicable)
- [ ] Security audit

### Monthly Tasks
- [ ] Full backup and test restore
- [ ] Performance audit
- [ ] Security scan
- [ ] Update SSL certificate (if expiring)
- [ ] Review and archive old logs

---

## Emergency Contacts

### Technical Support
- **Hosting Provider:** [Provider Name + Phone]
- **Domain Registrar:** [Registrar + Contact]
- **SSL Provider:** [Provider + Contact]

### Team Contacts
- **Lead Developer:** [Name + Contact]
- **Database Admin:** [Name + Contact]
- **System Admin:** [Name + Contact]

---

## Useful Commands

### Server Management
```bash
# Check disk space
df -h

# Check memory usage
free -m

# Check CPU usage
top

# Restart Apache
sudo systemctl restart apache2

# Restart Nginx
sudo systemctl restart nginx

# Restart MySQL
sudo systemctl restart mysql
```

### Database Management
```sql
-- Check database size
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'etectstore'
GROUP BY table_schema;

-- Optimize all tables
OPTIMIZE TABLE users, products, orders, order_items, cart, categories;

-- Check slow queries
SHOW VARIABLES LIKE 'slow_query_log';
```

### Log Management
```bash
# Rotate logs
logrotate -f /etc/logrotate.conf

# Clear old logs
find logs/ -name "*.log" -mtime +30 -delete

# Monitor real-time
tail -f logs/error.log | grep "ERROR"
```

---

## Success Criteria

### Performance
- [ ] Homepage loads in < 2 seconds
- [ ] Product page loads in < 1.5 seconds
- [ ] Checkout completes in < 3 seconds
- [ ] No database slow queries
- [ ] Server response time < 500ms

### Security
- [ ] All forms have CSRF protection
- [ ] No SQL injection vulnerabilities
- [ ] No XSS vulnerabilities
- [ ] HTTPS working properly
- [ ] Security headers configured
- [ ] File uploads validated

### Functionality
- [ ] All features working
- [ ] No 404 errors
- [ ] No PHP warnings/errors
- [ ] Database connections stable
- [ ] Email notifications working (if configured)

---

## Notes

### Known Limitations
- Cache directory requires write permission
- Image uploads limited to 2MB
- Session timeout set to 24 minutes
- Maximum 10 items per cart (if configured)

### Future Improvements Planned
- Payment gateway integration
- Email verification
- SMS notifications
- Advanced analytics
- Mobile app API

---

## Sign-off

**Deployed by:** _________________  
**Date:** _________________  
**Time:** _________________  

**Verified by:** _________________  
**Date:** _________________  
**Time:** _________________  

**Approved by:** _________________  
**Date:** _________________  
**Time:** _________________  

---

**Status:** 
- [ ] Ready for Staging
- [ ] Ready for Production
- [ ] Deployed to Staging
- [ ] Deployed to Production
- [ ] Post-deployment verified

**Last Updated:** 22 Desember 2025
