-- #!mysql

-- #{ table
    -- #{ generators
        CREATE TABLE IF NOT EXISTS generators (
            uuid BLOB PRIMARY KEY,
            level INTEGER NOT NULL DEFAULT 1
        );
    -- #}
-- #}

-- #{ generators
    -- #{ insert
        -- # :uuid blob
        INSERT IGNORE INTO generators (uuid, level) VALUES (:uuid, 1);
    -- #}

    -- #{ select_level
        -- # :uuid blob
        SELECT level FROM generators WHERE uuid = :uuid;
    -- #}

    -- #{ update_level
        -- # :uuid blob
        -- # :level int
        UPDATE generators SET level = :level WHERE uuid = :uuid;
    -- #}
-- #}