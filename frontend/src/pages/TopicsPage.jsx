import { useEffect, useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import * as topicsApi from "../api/topics";
import { useAuth } from "../context/AuthContext";
import { ErrorMessage, LoadingSpinner } from "../components/StatusMessage";
import Pagination from "../components/Pagination";

export default function TopicsPage() {
  const { isAuthenticated } = useAuth();
  const [searchParams, setSearchParams] = useSearchParams();
  const categoryId = searchParams.get("category_id") || "";
  const page = Number(searchParams.get("page") || 1);
  const sort = searchParams.get("sort") || "-created_at";

  const [topics, setTopics] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [titleFilter, setTitleFilter] = useState("");
  const [searchQuery, setSearchQuery] = useState("");
  const [searching, setSearching] = useState(false);

  function load() {
    setLoading(true);
    setError(null);
    const params = { page, sort };
    if (categoryId) params.category_id = categoryId;
    if (titleFilter) params.title = titleFilter;

    topicsApi
      .listTopics(params)
      .then((res) => {
        setTopics(res.data);
        setMeta(res.meta);
      })
      .catch(setError)
      .finally(() => setLoading(false));
  }

  useEffect(load, [categoryId, page, sort, titleFilter]);

  function updateParam(key, value) {
    const next = new URLSearchParams(searchParams);
    if (value) {
      next.set(key, value);
    } else {
      next.delete(key);
    }
    if (key !== "page") next.delete("page");
    setSearchParams(next);
  }

  async function handleSearch(e) {
    e.preventDefault();
    if (!searchQuery.trim()) return;
    setSearching(true);
    setError(null);
    try {
      const res = await topicsApi.searchTopics(searchQuery);
      setTopics(res.data);
      setMeta(null);
    } catch (err) {
      setError(err);
    } finally {
      setSearching(false);
    }
  }

  return (
    <div className="page">
      <div className="page-header">
        <h1>Teme{categoryId ? ` u kategoriji #${categoryId}` : ""}</h1>
        {isAuthenticated && <Link to="/topics/new">+ Nova tema</Link>}
      </div>

      <form onSubmit={handleSearch} className="toolbar">
        <input
          placeholder="Pretraga (naslov ili sadržaj)…"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
        />
        <button type="submit" disabled={searching}>
          Pretraži
        </button>
        <input
          placeholder="Filtriraj po naslovu…"
          value={titleFilter}
          onChange={(e) => setTitleFilter(e.target.value)}
        />
        <select value={sort} onChange={(e) => updateParam("sort", e.target.value)}>
          <option value="-created_at">Najnovije</option>
          <option value="created_at">Najstarije</option>
          <option value="title">Naslov A-Š</option>
        </select>
      </form>

      <ErrorMessage error={error} />
      {loading ? (
        <LoadingSpinner />
      ) : (
        <ul className="topic-list">
          {topics.map((topic) => (
            <li key={topic.id} className="topic-list-item">
              <Link to={`/topics/${topic.id}`}>{topic.title}</Link>
              <p className="topic-excerpt">{topic.body?.slice(0, 140)}</p>
            </li>
          ))}
          {topics.length === 0 && <p>Nema tema koje odgovaraju kriterijumu.</p>}
        </ul>
      )}
      {meta && (
        <Pagination
          meta={meta}
          onPageChange={(p) => updateParam("page", String(p))}
        />
      )}
    </div>
  );
}
