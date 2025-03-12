-- #!sqlite

-- #{ table
    -- #{ generators
        CREATE TABLE IF NOT EXISTS generators (
            uuid TEXT PRIMARY KEY,
            level INTEGER NOT NULL DEFAULT 1
        );
    -- #}
-- #}

-- #{ generators
    -- #{ insert
        -- # :uuid text
        INSERT OR IGNORE INTO generators (uuid, level) VALUES (:uuid, 1);
    -- #}

    -- #{ select_level
        -- # :uuid text
        SELECT level FROM generators WHERE uuid = :uuid;
    -- #}

    -- #{ update_level
        -- # :uuid text
        -- # :level int
        UPDATE generators SET level = :level WHERE uuid = :uuid;
    -- #}
-- #}
