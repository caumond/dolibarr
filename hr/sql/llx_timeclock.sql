-- ============================================================================
-- Copyright (C) 2019       Open-DSI <support@open-dsi.fr>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ============================================================================

create table llx_timeclock
(
  rowid				integer AUTO_INCREMENT PRIMARY KEY,
  fk_user			integer      NOT NULL,
  entity			integer DEFAULT 1 NOT NULL,	-- multi company id
  checking_arrival	datetime,                   -- informations arrivée
  ip_arrival		text,
  checking_exit		datetime,                   -- informations sortie
  ip_exit			text,
  fk_user_modif		integer,					-- Utilisateur qui a modifié une ligne
  tms				timestamp,					-- Date de modification
  status			integer						-- 1 = pointage en cours, 2=pointage effectué, -1 ligne desactivee
)ENGINE=innodb;