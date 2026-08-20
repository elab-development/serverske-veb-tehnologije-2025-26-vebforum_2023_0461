import { useEffect, useState } from "react";
import { Link, useSearchParams } from "react-router-dom";
import * as postsApi from "../api/posts";
import { ErrorMessage, LoadingSpinner } from "../components/StatusMessage";
import Pagination from "../components/Pagination";

export default function AllPostsPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const page = Number(searchParams.get("page") || 1);
  const [posts, setPosts] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    setLoading(true);
    setError(null);
    postsApi
      .listPosts({ page, sort: "-created_at" })
      .then((res) => {
        setPosts(res.data);
        setMeta(res.meta);
      })
      .catch(setError)
      .finally(() => setLoading(false));
  }, [page]);

  return (
    <div className="page">
      <h1>Svi postovi</h1>
      <ErrorMessage error={error} />
      {loading ? (
        <LoadingSpinner />
      ) : (
        <ul className="topic-list">
          {posts.map((post) => (
            <li key={post.id} className="topic-list-item">
              <Link to={`/topics/${post.topic_id}`}>
                Post u temi #{post.topic_id}
              </Link>
              <p className="topic-excerpt">{post.body?.slice(0, 160)}</p>
            </li>
          ))}
        </ul>
      )}
      <Pagination
        meta={meta}
        onPageChange={(p) =>
          setSearchParams({ page: String(p) })
        }
      />
    </div>
  );
}
