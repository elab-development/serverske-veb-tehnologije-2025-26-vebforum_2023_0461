import { useEffect, useState } from "react";
import * as usersApi from "../api/users";
import { ErrorMessage, LoadingSpinner } from "../components/StatusMessage";
import Pagination from "../components/Pagination";

const ROLES = ["user", "moderator", "admin"];

export default function AdminUsersPage() {
  const [users, setUsers] = useState([]);
  const [meta, setMeta] = useState(null);
  const [page, setPage] = useState(1);
  const [roleFilter, setRoleFilter] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [savingId, setSavingId] = useState(null);

  function load() {
    setLoading(true);
    setError(null);
    const params = { page };
    if (roleFilter) params.role = roleFilter;
    usersApi
      .listUsers(params)
      .then((res) => {
        setUsers(res.data);
        setMeta(res.meta);
      })
      .catch(setError)
      .finally(() => setLoading(false));
  }

  useEffect(load, [page, roleFilter]);

  async function handleRoleChange(id, role) {
    setSavingId(id);
    setError(null);
    try {
      await usersApi.updateUserRole(id, role);
      load();
    } catch (err) {
      setError(err);
    } finally {
      setSavingId(null);
    }
  }

  return (
    <div className="page">
      <h1>Korisnici</h1>
      <div className="toolbar">
        <select value={roleFilter} onChange={(e) => setRoleFilter(e.target.value)}>
          <option value="">Sve uloge</option>
          {ROLES.map((role) => (
            <option key={role} value={role}>
              {role}
            </option>
          ))}
        </select>
      </div>

      <ErrorMessage error={error} />
      {loading ? (
        <LoadingSpinner />
      ) : (
        <table className="data-table">
          <thead>
            <tr>
              <th>Ime</th>
              <th>Email</th>
              <th>Uloga</th>
            </tr>
          </thead>
          <tbody>
            {users.map((u) => (
              <tr key={u.id}>
                <td>{u.name}</td>
                <td>{u.email}</td>
                <td>
                  <select
                    value={u.role}
                    disabled={savingId === u.id}
                    onChange={(e) => handleRoleChange(u.id, e.target.value)}
                  >
                    {ROLES.map((role) => (
                      <option key={role} value={role}>
                        {role}
                      </option>
                    ))}
                  </select>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
      <Pagination meta={meta} onPageChange={setPage} />
    </div>
  );
}
