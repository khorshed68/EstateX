-- ============================================================
-- EstateX Admin Database Schema, Views, Triggers, & Procedures
-- ============================================================

-- 1. Admin Audit Trail Table and Auto-Increment Sequences
BEGIN EXECUTE IMMEDIATE 'DROP TABLE admin_audit_logs CASCADE CONSTRAINTS'; EXCEPTION WHEN OTHERS THEN NULL; END;
/
BEGIN EXECUTE IMMEDIATE 'DROP SEQUENCE seq_admin_audit_logs'; EXCEPTION WHEN OTHERS THEN NULL; END;
/

CREATE TABLE admin_audit_logs (
    id            NUMBER PRIMARY KEY,
    adminUserId   NUMBER NOT NULL,
    actionName    VARCHAR2(100) NOT NULL,
    tableName     VARCHAR2(50) NOT NULL,
    recordId      NUMBER NOT NULL,
    oldValues     VARCHAR2(1000),
    newValues     VARCHAR2(1000),
    performedAt   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_admin FOREIGN KEY (adminUserId) REFERENCES users(id) ON DELETE CASCADE
);
/

CREATE SEQUENCE seq_admin_audit_logs START WITH 1 INCREMENT BY 1;
/

CREATE OR REPLACE TRIGGER trg_admin_audit_logs_id
BEFORE INSERT ON admin_audit_logs
FOR EACH ROW
BEGIN
    IF :NEW.id IS NULL THEN
        SELECT seq_admin_audit_logs.NEXTVAL INTO :NEW.id FROM dual;
    END IF;
END;
/


-- 2. Audit Triggers for Automatic Log Generation
CREATE OR REPLACE TRIGGER trg_audit_user_changes
AFTER UPDATE ON users
FOR EACH ROW
DECLARE
    v_admin_id NUMBER;
BEGIN
    -- Log status or role changes done to users
    IF :OLD.status != :NEW.status OR :OLD.roleId != :NEW.roleId THEN
        -- In a real DB application, the client/app user ID can be fetched from a session variable
        -- If session context isn't set, default to system admin user (assumed roleId=1, user=1)
        v_admin_id := NVL(SYS_CONTEXT('USERENV', 'CLIENT_IDENTIFIER'), 1);
        
        INSERT INTO admin_audit_logs (
            adminUserId, actionName, tableName, recordId, oldValues, newValues
        ) VALUES (
            v_admin_id,
            'USER_MODIFICATION',
            'USERS',
            :NEW.id,
            'Status: ' || :OLD.status || ', Role: ' || :OLD.roleId,
            'Status: ' || :NEW.status || ', Role: ' || :NEW.roleId
        );
    END IF;
END;
/

CREATE OR REPLACE TRIGGER trg_audit_property_changes
AFTER UPDATE OR DELETE ON properties
FOR EACH ROW
DECLARE
    v_admin_id NUMBER;
BEGIN
    v_admin_id := NVL(SYS_CONTEXT('USERENV', 'CLIENT_IDENTIFIER'), 1);
    
    IF UPDATING THEN
        IF :OLD.status != :NEW.status OR :OLD.price != :NEW.price THEN
            INSERT INTO admin_audit_logs (
                adminUserId, actionName, tableName, recordId, oldValues, newValues
            ) VALUES (
                v_admin_id,
                'PROPERTY_UPDATE',
                'PROPERTIES',
                :NEW.id,
                'Status: ' || :OLD.status || ', Price: ' || :OLD.price,
                'Status: ' || :NEW.status || ', Price: ' || :NEW.price
            );
        END IF;
    ELSIF DELETING THEN
        INSERT INTO admin_audit_logs (
            adminUserId, actionName, tableName, recordId, oldValues, newValues
        ) VALUES (
            v_admin_id,
            'PROPERTY_DELETE',
            'PROPERTIES',
            :OLD.id,
            'Title: ' || :OLD.title || ', Price: ' || :OLD.price,
            'DELETED'
        );
    END IF;
END;
/


-- 3. Advanced Admin Dashboard Views
CREATE OR REPLACE VIEW v_admin_dashboard_summary AS
SELECT
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM users WHERE status = 'active') AS total_active_users,
    (SELECT COUNT(*) FROM properties) AS total_listings,
    (SELECT COUNT(*) FROM properties WHERE status = 'available') AS active_listings,
    (SELECT COUNT(*) FROM bookings) AS total_bookings,
    (SELECT COUNT(*) FROM bookings WHERE status = 'completed') AS completed_bookings,
    (SELECT NVL(SUM(amount), 0) FROM transactions WHERE status = 'completed') AS total_revenue
FROM dual;
/

CREATE OR REPLACE VIEW v_monthly_revenue_trend AS
SELECT 
    TO_CHAR(transactionDate, 'YYYY-MM') AS month,
    COUNT(id) AS total_transactions,
    SUM(amount) AS total_revenue,
    AVG(amount) AS avg_deal_value
FROM transactions
WHERE status = 'completed'
GROUP BY TO_CHAR(transactionDate, 'YYYY-MM');
/

CREATE OR REPLACE VIEW v_trending_locations AS
SELECT 
    l.city,
    l.areaName,
    COUNT(p.id) AS total_listings,
    ROUND(AVG(p.price), 2) AS avg_price,
    COUNT(b.id) AS total_bookings_made
FROM locations l
LEFT JOIN properties p ON p.locationId = l.id
LEFT JOIN bookings b ON b.propertyId = p.id
GROUP BY l.city, l.areaName;
/


-- 4. Optimized Indexes for Admin Reporting Queries
CREATE INDEX idx_users_role_status ON users (roleId, status);
CREATE INDEX idx_properties_agent_status ON properties (agentId, status);
CREATE INDEX idx_properties_location ON properties (locationId);
CREATE INDEX idx_transactions_status_date ON transactions (status, transactionDate);
CREATE INDEX idx_bookings_status_date ON bookings (status, visitDate);
/


-- 5. Stored Procedures Package Specification (PKG_ESTATEX_ADMIN)
CREATE OR REPLACE PACKAGE PKG_ESTATEX_ADMIN AS

    -- Fetch primary dashboard metrics
    PROCEDURE get_dashboard_summary_kpis (
        o_total_users     OUT NUMBER,
        o_total_listings  OUT NUMBER,
        o_total_revenue   OUT NUMBER,
        o_total_bookings  OUT NUMBER,
        o_success_rate    OUT NUMBER
    );

    -- Cursor for monthly revenue charts
    PROCEDURE get_monthly_revenue_chart (
        o_cursor OUT SYS_REFCURSOR
    );

    -- Cursor for agent leaderboard
    PROCEDURE get_agent_leaderboard (
        o_cursor OUT SYS_REFCURSOR
    );

    -- Cursor for trending/hot locations
    PROCEDURE get_hot_locations_chart (
        o_cursor OUT SYS_REFCURSOR
    );

    -- Cursor for top trending properties
    PROCEDURE get_trending_properties (
        o_cursor OUT SYS_REFCURSOR
    );

    -- Administrative Actions
    PROCEDURE suspend_user (
        p_user_id   IN NUMBER,
        p_admin_id  IN NUMBER,
        p_reason    IN VARCHAR2
    );

    PROCEDURE activate_user (
        p_user_id   IN NUMBER,
        p_admin_id  IN NUMBER
    );

    PROCEDURE delete_property_listing (
        p_property_id IN NUMBER,
        p_admin_id    IN NUMBER
    );

END PKG_ESTATEX_ADMIN;
/


-- 6. Stored Procedures Package Body (PKG_ESTATEX_ADMIN)
CREATE OR REPLACE PACKAGE BODY PKG_ESTATEX_ADMIN AS

    PROCEDURE get_dashboard_summary_kpis (
        o_total_users     OUT NUMBER,
        o_total_listings  OUT NUMBER,
        o_total_revenue   OUT NUMBER,
        o_total_bookings  OUT NUMBER,
        o_success_rate    OUT NUMBER
    ) AS
        v_completed_bookings NUMBER;
    BEGIN
        SELECT total_users, total_listings, total_revenue, total_bookings, completed_bookings
        INTO o_total_users, o_total_listings, o_total_revenue, o_total_bookings, v_completed_bookings
        FROM v_admin_dashboard_summary;

        IF o_total_bookings > 0 THEN
            o_success_rate := ROUND((v_completed_bookings / o_total_bookings) * 100, 2);
        ELSE
            o_success_rate := 0.00;
        END IF;
    END get_dashboard_summary_kpis;

    PROCEDURE get_monthly_revenue_chart (
        o_cursor OUT SYS_REFCURSOR
    ) AS
    BEGIN
        OPEN o_cursor FOR
            SELECT month, total_transactions, total_revenue, avg_deal_value
            FROM v_monthly_revenue_trend
            ORDER BY month ASC;
    END get_monthly_revenue_chart;

    PROCEDURE get_agent_leaderboard (
        o_cursor OUT SYS_REFCURSOR
    ) AS
    BEGIN
        OPEN o_cursor FOR
            SELECT a.id AS agent_id,
                   u.fullname AS agent_name,
                   a.agencyName,
                   a.rating AS avg_rating,
                   COUNT(DISTINCT p.id) AS active_listings,
                   COUNT(DISTINCT b.id) AS completed_deals,
                   NVL(SUM(t.amount), 0) AS total_revenue
            FROM agents a
            JOIN users u ON a.userId = u.id
            LEFT JOIN properties p ON p.agentId = a.id
            LEFT JOIN bookings b ON b.propertyId = p.id AND b.status = 'completed'
            LEFT JOIN transactions t ON t.bookingId = b.id AND t.status = 'completed'
            GROUP BY a.id, u.fullname, a.agencyName, a.rating
            ORDER BY total_revenue DESC, avg_rating DESC;
    END get_agent_leaderboard;

    PROCEDURE get_hot_locations_chart (
        o_cursor OUT SYS_REFCURSOR
    ) AS
    BEGIN
        OPEN o_cursor FOR
            SELECT city, areaName, total_listings, avg_price, total_bookings_made
            FROM v_trending_locations
            ORDER BY total_bookings_made DESC, total_listings DESC;
    END get_hot_locations_chart;

    PROCEDURE get_trending_properties (
        o_cursor OUT SYS_REFCURSOR
    ) AS
    BEGIN
        OPEN o_cursor FOR
            SELECT p.id AS property_id,
                   p.title,
                   p.price,
                   l.areaName,
                   l.city,
                   COUNT(DISTINCT w.id) AS wishlist_count,
                   COUNT(DISTINCT b.id) AS bookings_count,
                   (COUNT(DISTINCT w.id) * 10 + COUNT(DISTINCT b.id) * 30) AS trend_score
            FROM properties p
            JOIN locations l ON p.locationId = l.id
            LEFT JOIN wishlist w ON w.propertyId = p.id
            LEFT JOIN bookings b ON b.propertyId = p.id
            GROUP BY p.id, p.title, p.price, l.areaName, l.city
            ORDER BY trend_score DESC;
    END get_trending_properties;

    PROCEDURE suspend_user (
        p_user_id   IN NUMBER,
        p_admin_id  IN NUMBER,
        p_reason    IN VARCHAR2
    ) AS
    BEGIN
        -- Set system context identifier to log admin ID in audit triggers
        DBMS_SESSION.SET_IDENTIFIER(TO_CHAR(p_admin_id));
        
        UPDATE users
        SET status = 'suspended'
        WHERE id = p_user_id;
        
        COMMIT;
    END suspend_user;

    PROCEDURE activate_user (
        p_user_id   IN NUMBER,
        p_admin_id  IN NUMBER
    ) AS
    BEGIN
        DBMS_SESSION.SET_IDENTIFIER(TO_CHAR(p_admin_id));
        
        UPDATE users
        SET status = 'active'
        WHERE id = p_user_id;
        
        COMMIT;
    END activate_user;

    PROCEDURE delete_property_listing (
        p_property_id IN NUMBER,
        p_admin_id    IN NUMBER
    ) AS
    BEGIN
        DBMS_SESSION.SET_IDENTIFIER(TO_CHAR(p_admin_id));
        
        DELETE FROM properties
        WHERE id = p_property_id;
        
        COMMIT;
    END delete_property_listing;

END PKG_ESTATEX_ADMIN;
/
