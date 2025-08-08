# coding: utf-8
from sqlalchemy import BigInteger, CHAR, Column, DateTime, Enum, ForeignKey, Integer, String, TIMESTAMP, Text, text
from sqlalchemy.dialects.mysql import BIGINT, INTEGER, LONGTEXT, MEDIUMTEXT, TIMESTAMP, TINYINT
from sqlalchemy.orm import relationship
from sqlalchemy.ext.declarative import declarative_base

Base = declarative_base()
metadata = Base.metadata


class Cache(Base):
    __tablename__ = 'cache'

    key = Column(String(255), primary_key=True)
    value = Column(MEDIUMTEXT, nullable=False)
    expiration = Column(Integer, nullable=False)


class CacheLock(Base):
    __tablename__ = 'cache_locks'

    key = Column(String(255), primary_key=True)
    owner = Column(String(255), nullable=False)
    expiration = Column(Integer, nullable=False)


class Country(Base):
    __tablename__ = 'countries'

    id = Column(BIGINT, primary_key=True)
    country_name = Column(String(255), nullable=False)
    iso2 = Column(String(255), nullable=False)
    iso3 = Column(String(255), nullable=False)
    top_level_domain = Column(String(255), nullable=False)
    fips = Column(String(255), nullable=False)
    iso_numeric = Column(Integer, nullable=False)
    geo_name_id = Column(Integer)
    e164 = Column(Integer, nullable=False)
    phone_code = Column(String(255))
    continent = Column(String(255), nullable=False)
    capital = Column(String(255), nullable=False)
    time_zone_in_capital = Column(String(255), nullable=False)
    currency = Column(String(255))
    created_at = Column(TIMESTAMP)
    updated_at = Column(TIMESTAMP)


class FailedJob(Base):
    __tablename__ = 'failed_jobs'

    id = Column(BIGINT, primary_key=True)
    uuid = Column(String(255), nullable=False, unique=True)
    connection = Column(Text, nullable=False)
    queue = Column(Text, nullable=False)
    payload = Column(LONGTEXT, nullable=False)
    exception = Column(LONGTEXT, nullable=False)
    failed_at = Column(TIMESTAMP, nullable=False, server_default=text("CURRENT_TIMESTAMP"))


class JobBatch(Base):
    __tablename__ = 'job_batches'

    id = Column(String(255), primary_key=True)
    name = Column(String(255), nullable=False)
    total_jobs = Column(Integer, nullable=False)
    pending_jobs = Column(Integer, nullable=False)
    failed_jobs = Column(Integer, nullable=False)
    failed_job_ids = Column(LONGTEXT, nullable=False)
    options = Column(MEDIUMTEXT)
    cancelled_at = Column(Integer)
    created_at = Column(Integer, nullable=False)
    finished_at = Column(Integer)


class Job(Base):
    __tablename__ = 'jobs'

    id = Column(BIGINT, primary_key=True)
    queue = Column(String(255), nullable=False, index=True)
    payload = Column(LONGTEXT, nullable=False)
    attempts = Column(TINYINT, nullable=False)
    reserved_at = Column(INTEGER)
    available_at = Column(INTEGER, nullable=False)
    created_at = Column(INTEGER, nullable=False)


class Migration(Base):
    __tablename__ = 'migrations'

    id = Column(INTEGER, primary_key=True)
    migration = Column(String(255), nullable=False)
    batch = Column(Integer, nullable=False)


class Na(Base):
    __tablename__ = 'nas'

    id = Column(Integer, primary_key=True)
    nasname = Column(String(128), nullable=False, index=True)
    shortname = Column(String(32))
    type = Column(String(30), server_default=text("'other'"))
    ports = Column(Integer)
    secret = Column(String(60), nullable=False, server_default=text("'secret'"))
    server = Column(String(64))
    community = Column(String(50))
    description = Column(String(200), server_default=text("'RADIUS Client'"))


class Nasreload(Base):
    __tablename__ = 'nasreload'

    nasipaddress = Column(String(15), primary_key=True)
    reloadtime = Column(DateTime, nullable=False)


class PasswordResetToken(Base):
    __tablename__ = 'password_reset_tokens'

    email = Column(String(255), primary_key=True)
    token = Column(String(255), nullable=False)
    created_at = Column(TIMESTAMP)


class Radacct(Base):
    __tablename__ = 'radacct'

    radacctid = Column(BigInteger, primary_key=True)
    acctsessionid = Column(String(64), nullable=False, index=True, server_default=text("''"))
    acctuniqueid = Column(String(32), nullable=False, unique=True, server_default=text("''"))
    username = Column(String(64), nullable=False, index=True, server_default=text("''"))
    realm = Column(String(64), server_default=text("''"))
    nasipaddress = Column(String(15), nullable=False, index=True, server_default=text("''"))
    nasportid = Column(String(32))
    nasporttype = Column(String(32))
    acctstarttime = Column(DateTime, index=True)
    acctupdatetime = Column(DateTime)
    acctstoptime = Column(DateTime, index=True)
    acctinterval = Column(Integer, index=True)
    acctsessiontime = Column(INTEGER, index=True)
    acctauthentic = Column(String(32))
    connectinfo_start = Column(String(128))
    connectinfo_stop = Column(String(128))
    acctinputoctets = Column(BigInteger)
    acctoutputoctets = Column(BigInteger)
    calledstationid = Column(String(50), nullable=False, server_default=text("''"))
    callingstationid = Column(String(50), nullable=False, server_default=text("''"))
    acctterminatecause = Column(String(32), nullable=False, server_default=text("''"))
    servicetype = Column(String(32))
    framedprotocol = Column(String(32))
    framedipaddress = Column(String(15), nullable=False, index=True, server_default=text("''"))
    framedipv6address = Column(String(45), nullable=False, index=True, server_default=text("''"))
    framedipv6prefix = Column(String(45), nullable=False, index=True, server_default=text("''"))
    framedinterfaceid = Column(String(44), nullable=False, index=True, server_default=text("''"))
    delegatedipv6prefix = Column(String(45), nullable=False, index=True, server_default=text("''"))
    _class = Column('class', String(64), index=True)


class Radcheck(Base):
    __tablename__ = 'radcheck'

    id = Column(INTEGER, primary_key=True)
    username = Column(String(64), nullable=False, index=True, server_default=text("''"))
    attribute = Column(String(64), nullable=False, server_default=text("''"))
    op = Column(CHAR(2), nullable=False, server_default=text("'=='"))
    value = Column(String(253), nullable=False, server_default=text("''"))


class Radgroupcheck(Base):
    __tablename__ = 'radgroupcheck'

    id = Column(INTEGER, primary_key=True)
    groupname = Column(String(64), nullable=False, index=True, server_default=text("''"))
    attribute = Column(String(64), nullable=False, server_default=text("''"))
    op = Column(CHAR(2), nullable=False, server_default=text("'=='"))
    value = Column(String(253), nullable=False, server_default=text("''"))


class Radgroupreply(Base):
    __tablename__ = 'radgroupreply'

    id = Column(INTEGER, primary_key=True)
    groupname = Column(String(64), nullable=False, index=True, server_default=text("''"))
    attribute = Column(String(64), nullable=False, server_default=text("''"))
    op = Column(CHAR(2), nullable=False, server_default=text("'='"))
    value = Column(String(253), nullable=False, server_default=text("''"))


class Radpostauth(Base):
    __tablename__ = 'radpostauth'

    id = Column(Integer, primary_key=True)
    username = Column(String(64), nullable=False, index=True, server_default=text("''"))
    _pass = Column('pass', String(64), nullable=False, server_default=text("''"))
    reply = Column(String(32), nullable=False, server_default=text("''"))
    authdate = Column(TIMESTAMP(fsp=6), nullable=False, server_default=text("CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)"))
    _class = Column('class', String(64), index=True)


class Radreply(Base):
    __tablename__ = 'radreply'

    id = Column(INTEGER, primary_key=True)
    username = Column(String(64), nullable=False, index=True, server_default=text("''"))
    attribute = Column(String(64), nullable=False, server_default=text("''"))
    op = Column(CHAR(2), nullable=False, server_default=text("'='"))
    value = Column(String(253), nullable=False, server_default=text("''"))


class Radusergroup(Base):
    __tablename__ = 'radusergroup'

    id = Column(INTEGER, primary_key=True)
    username = Column(String(64), nullable=False, index=True, server_default=text("''"))
    groupname = Column(String(64), nullable=False, server_default=text("''"))
    priority = Column(Integer, nullable=False, server_default=text("'1'"))


class Session(Base):
    __tablename__ = 'sessions'

    id = Column(String(255), primary_key=True)
    user_id = Column(BIGINT, index=True)
    ip_address = Column(String(45))
    user_agent = Column(Text)
    payload = Column(LONGTEXT, nullable=False)
    last_activity = Column(Integer, nullable=False, index=True)


class Telegram(Base):
    __tablename__ = 'telegrams'

    id = Column(BIGINT, primary_key=True)
    telegram_id = Column(String(255), nullable=False)
    first_name = Column(String(255))
    last_name = Column(String(255))
    username = Column(String(255))
    phone = Column(String(255))
    verified = Column(Enum('0', '1'), nullable=False, server_default=text("'0'"))
    role = Column(Enum('admin', 'user'), nullable=False, server_default=text("'user'"))
    verified_at = Column(DateTime)
    created_at = Column(TIMESTAMP)
    updated_at = Column(TIMESTAMP)


class User(Base):
    __tablename__ = 'users'

    id = Column(BIGINT, primary_key=True)
    name = Column(String(255), nullable=False)
    email = Column(String(255), nullable=False, unique=True)
    username = Column(String(255), nullable=False, unique=True)
    type = Column(Enum('admin', 'operator', 'user'), nullable=False, server_default=text("'user'"))
    email_verified_at = Column(TIMESTAMP)
    password = Column(String(255), nullable=False)
    remember_token = Column(String(100))
    created_at = Column(TIMESTAMP)
    updated_at = Column(TIMESTAMP)


class Guest(Base):
    __tablename__ = 'guests'

    id = Column(CHAR(36), primary_key=True, server_default=text("(uuid())"))
    name = Column(String(255), nullable=False)
    email = Column(String(255), nullable=False, unique=True)
    country_id = Column(ForeignKey('countries.id', ondelete='CASCADE'), nullable=False, index=True)
    username = Column(String(255), nullable=False, unique=True)
    password = Column(String(255), nullable=False)
    mac_add = Column(String(255))
    os_client = Column(String(255))
    browser_client = Column(String(255))
    created_at = Column(TIMESTAMP)
    updated_at = Column(TIMESTAMP)
    device_client = Column(String(255))
    brand_client = Column(String(255))
    model_client = Column(String(255))
    device_type = Column(String(255))

    country = relationship('Country')
