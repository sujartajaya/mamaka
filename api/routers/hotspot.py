from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy.orm import Session
from sqlalchemy.ext.asyncio import AsyncSession
from typing import List, Optional, Union

from api.models import models
from api.schemas.radreply import RadreplyCreate, RadreplyResponse
from api.database.session import get_db
from api.utils.security import role_required  # kalau ada role check

router = APIRouter()


# ✅ Create
@router.post("/radreply", response_model=RadreplyResponse)
async def create_radreply(
    data: RadreplyCreate,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator"]))
):
    new_entry = models.Radreply(
        username=data.username,
        attribute=data.attribute,
        op=data.op,
        value=data.value
    )
    if isinstance(db, AsyncSession):
        db.add(new_entry)
        await db.commit()
        await db.refresh(new_entry)
    else:
        db.add(new_entry)
        db.commit()
        db.refresh(new_entry)

    return new_entry


# ✅ Update (by id)
@router.put("/radreply/{id}", response_model=RadreplyResponse)
async def update_radreply(
    id: int,
    data: RadreplyCreate,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator"]))
):
    stmt = None
    if isinstance(db, AsyncSession):
        db_obj = await db.get(models.Radreply, id)
    else:
        db_obj = db.get(models.Radreply, id)

    if not db_obj:
        raise HTTPException(status_code=404, detail="Radreply not found")

    db_obj.username = data.username
    db_obj.attribute = data.attribute
    db_obj.op = data.op
    db_obj.value = data.value

    if isinstance(db, AsyncSession):
        db.add(db_obj)
        await db.commit()
        await db.refresh(db_obj)
    else:
        db.add(db_obj)
        db.commit()
        db.refresh(db_obj)

    return db_obj


# ✅ Delete (by id)
@router.delete("/radreply/{id}")
async def delete_radreply(
    id: int,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin"]))
):
    if isinstance(db, AsyncSession):
        db_obj = await db.get(models.Radreply, id)
    else:
        db_obj = db.get(models.Radreply, id)

    if not db_obj:
        raise HTTPException(status_code=404, detail="Radreply not found")

    if isinstance(db, AsyncSession):
        await db.delete(db_obj)
        await db.commit()
    else:
        db.delete(db_obj)
        db.commit()

    return {"message": "Radreply deleted successfully"}


# ✅ Search by username
@router.get("/radreply/search", response_model=List[RadreplyResponse])
async def search_radreply(
    username: str = Query(..., description="Username to search"),
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator", "user"]))
):
    stmt = models.Radreply.__table__.select().where(models.Radreply.username == username)

    if isinstance(db, AsyncSession):
        result = await db.execute(stmt)
        rows = result.fetchall()
    else:
        result = db.execute(stmt)
        rows = result.fetchall()

    return [RadreplyResponse.from_orm(r) for r in rows]


from api.schemas.radcheck import RadcheckCreate, RadcheckResponse

# ✅ Create
@router.post("/radcheck", response_model=RadcheckResponse)
async def create_radcheck(
    data: RadcheckCreate,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator"]))
):
    new_entry = models.Radcheck(
        username=data.username,
        attribute=data.attribute,
        op=data.op,
        value=data.value
    )
    if isinstance(db, AsyncSession):
        db.add(new_entry)
        await db.commit()
        await db.refresh(new_entry)
    else:
        db.add(new_entry)
        db.commit()
        db.refresh(new_entry)

    return new_entry


# ✅ Update (by id)
@router.put("/radcheck/{id}", response_model=RadcheckResponse)
async def update_radcheck(
    id: int,
    data: RadcheckCreate,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator"]))
):
    if isinstance(db, AsyncSession):
        db_obj = await db.get(models.Radcheck, id)
    else:
        db_obj = db.get(models.Radcheck, id)

    if not db_obj:
        raise HTTPException(status_code=404, detail="Radcheck not found")

    db_obj.username = data.username
    db_obj.attribute = data.attribute
    db_obj.op = data.op
    db_obj.value = data.value

    if isinstance(db, AsyncSession):
        db.add(db_obj)
        await db.commit()
        await db.refresh(db_obj)
    else:
        db.add(db_obj)
        db.commit()
        db.refresh(db_obj)

    return db_obj


# ✅ Delete (by id)
@router.delete("/radcheck/{id}")
async def delete_radcheck(
    id: int,
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin"]))
):
    if isinstance(db, AsyncSession):
        db_obj = await db.get(models.Radcheck, id)
    else:
        db_obj = db.get(models.Radcheck, id)

    if not db_obj:
        raise HTTPException(status_code=404, detail="Radcheck not found")

    if isinstance(db, AsyncSession):
        await db.delete(db_obj)
        await db.commit()
    else:
        db.delete(db_obj)
        db.commit()

    return {"message": "Radcheck deleted successfully"}


# ✅ Search by username
@router.get("/radcheck/search", response_model=List[RadcheckResponse])
async def search_radcheck(
    username: str = Query(..., description="Username to search"),
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator", "user"]))
):
    stmt = models.Radcheck.__table__.select().where(models.Radcheck.username == username)

    if isinstance(db, AsyncSession):
        result = await db.execute(stmt)
        rows = result.fetchall()
    else:
        result = db.execute(stmt)
        rows = result.fetchall()

    return [RadcheckResponse.from_orm(r) for r in rows]


from api.schemas.radius import RadiusUserResponse

# ✅ Search gabungan Radcheck + Radreply
@router.get("/radius/search", response_model=RadiusUserResponse)
async def search_radius_user(
    username: str = Query(..., description="Username to search"),
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator", "user"]))
):
    # Query radcheck
    stmt_check = models.Radcheck.__table__.select().where(models.Radcheck.username == username)
    # Query radreply
    stmt_reply = models.Radreply.__table__.select().where(models.Radreply.username == username)

    if isinstance(db, AsyncSession):
        result_check = await db.execute(stmt_check)
        rows_check = result_check.fetchall()

        result_reply = await db.execute(stmt_reply)
        rows_reply = result_reply.fetchall()
    else:
        result_check = db.execute(stmt_check)
        rows_check = result_check.fetchall()

        result_reply = db.execute(stmt_reply)
        rows_reply = result_reply.fetchall()

    return RadiusUserResponse(
        username=username,
        radcheck=[RadcheckResponse.from_orm(r) for r in rows_check],
        radreply=[RadreplyResponse.from_orm(r) for r in rows_reply]
    )



from sqlalchemy import select, union_all, literal
from sqlalchemy.ext.asyncio import AsyncSession
from fastapi import Depends, HTTPException, APIRouter
from typing import Union

@router.get("/radius/all")
async def get_all_radius_users(
    db: Union[Session, AsyncSession] = Depends(get_db),
    user=Depends(role_required(["admin", "operator"]))
):
    """
    Ambil semua username unik dari radcheck dan radreply
    """
    try:
        # ambil username dari radcheck
        stmt_radcheck = select(models.Radcheck.username, literal("radcheck").label("source"))
        # ambil username dari radreply
        stmt_radreply = select(models.Radreply.username, literal("radreply").label("source"))

        # gabung keduanya
        stmt = union_all(stmt_radcheck, stmt_radreply)
        result = await db.execute(stmt)
        rows = result.all()

        # kumpulkan ke dict {username: {"radcheck": bool, "radreply": bool}}
        user_map = {}
        for row in rows:
            uname = row.username
            if uname not in user_map:
                user_map[uname] = {"username": uname, "radcheck": False, "radreply": False}
            if row.source == "radcheck":
                user_map[uname]["radcheck"] = True
            elif row.source == "radreply":
                user_map[uname]["radreply"] = True

        return list(user_map.values())

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Database error: {str(e)}")
