import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import * as categoriesApi from "../api/categories";
import * as topicsApi from "../api/topics";
import { useAuth } from "../context/AuthContext";
import { ErrorMessage, LoadingSpinner } from "../components/StatusMessage";
import Pagination from "../components/Pagination";

export default function HomePage() {
  const { isAdmin } = useAuth();
  const [categories, setCategories] = useState([]);
  const [meta, setMeta] = useState(null);
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [form, setForm] = useState({ name: "", description: "" });
  const [editingId, setEditingId] = useState(null);
  const [starter, setStarter] = useState(null);

  function load() {
    setLoading(true);
    setError(null);
    categoriesApi
      .listCategories({ page })
      .then((res) => {
        setCategories(res.data);
        setMeta(res.meta);
      })
      .catch(setError)
      .finally(() => setLoading(false));
  }

  useEffect(load, [page]);

  useEffect(() => {
    topicsApi
      .discussionStarter()
      .then((res) => setStarter(res.data))
      .catch(() => setStarter(null));
  }, []);

  async function handleCreateOrUpdate(e) {
    e.preventDefault();
    try {
      if (editingId) {
        await categoriesApi.updateCategory(editingId, form);
      } else {
        await categoriesApi.createCategory(form);
      }
      setForm({ name: "", description: "" });
      setEditingId(null);
      load();
    } catch (err) {
      setError(err);
    }
  }

  function startEdit(category) {
    setEditingId(category.id);
    setForm({ name: category.name, description: category.description || "" });
  }

  async function handleDelete(id) {
    if (!confirm("Obrisati kategoriju?")) return;
    try {
      await categoriesApi.deleteCategory(id);
      load();
    } catch (err) {
      setError(err);
    }
  }

  return (
    <div className="page">
      <h1>Kategorije</h1>

      {starter && (
        <div className="discussion-starter">
          <p className="discussion-quote">"{starter.quote}"</p>
          <p className="discussion-author">— {starter.author}</p>
          <p className="discussion-hint">
            Predlog za novu temu: <strong>{starter.suggested_title}</strong>{" "}
            ({starter.word_count} reči)
          </p>
        </div>
      )}

      <ErrorMessage error={error} />
      {loading ? (
        <LoadingSpinner />
      ) : (
        <div className="card-grid">
          {categories.map((category) => (
            <div key={category.id} className="card">
              <Link to={`/topics?category_id=${category.id}`}>
                <h3>{category.name}</h3>
              </Link>
              <p>{category.description}</p>
              {isAdmin && (
                <div className="card-actions">
                  <button type="button" onClick={() => startEdit(category)}>
                    Izmeni
                  </button>
                  <button
                    type="button"
                    onClick={() => handleDelete(category.id)}
                  >
                    Obriši
                  </button>
                </div>
              )}
            </div>
          ))}
        </div>
      )}
      <Pagination meta={meta} onPageChange={setPage} />

      {isAdmin && (
        <div className="admin-panel">
          <h2>{editingId ? "Izmena kategorije" : "Nova kategorija"}</h2>
          <form onSubmit={handleCreateOrUpdate} className="form form-inline">
            <input
              placeholder="Naziv"
              required
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
            />
            <input
              placeholder="Opis"
              value={form.description}
              onChange={(e) =>
                setForm({ ...form, description: e.target.value })
              }
            />
            <button type="submit">{editingId ? "Sačuvaj" : "Dodaj"}</button>
            {editingId && (
              <button
                type="button"
                onClick={() => {
                  setEditingId(null);
                  setForm({ name: "", description: "" });
                }}
              >
                Otkaži
              </button>
            )}
          </form>
        </div>
      )}
    </div>
  );
}
