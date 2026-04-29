import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import { getAdvisor, getAdvisors } from "@/lib/api";
import { Container } from "@/components/Container";
import { JsonLd } from "@/components/JsonLd";
import { env } from "@/lib/env";

type Params = { slug: string };

export async function generateStaticParams(): Promise<Params[]> {
  const { items } = await getAdvisors();
  return items.map((a) => ({ slug: a.slug }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<Params>;
}): Promise<Metadata> {
  const { slug } = await params;
  const data = await getAdvisor(slug);
  if (!data) return { title: "Not found" };
  return {
    title: data.advisor.name,
    description: data.advisor.role,
  };
}

export default async function AdvisorDetailPage({
  params,
}: {
  params: Promise<Params>;
}) {
  const { slug } = await params;
  const data = await getAdvisor(slug);
  if (!data) notFound();

  const { advisor, divisions } = data;
  const initials = advisor.name
    .split(" ")
    .map((p) => p[0])
    .filter(Boolean)
    .slice(0, 2)
    .join("");

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "Person",
    name: advisor.name,
    jobTitle: advisor.role,
    url: `${env.SITE_URL}/advisors/${advisor.slug}`,
    ...(advisor.email && { email: advisor.email }),
    ...(advisor.linkedin_url && { sameAs: [advisor.linkedin_url] }),
    worksFor: {
      "@type": "Organization",
      name: "Northium Financial",
      url: env.SITE_URL,
    },
  };

  return (
    <>
      <JsonLd data={jsonLd} />
      <section className="border-b border-border">
        <Container className="py-16">
          <Link
            href="/advisors"
            className="text-xs uppercase tracking-[0.2em] text-muted hover:text-text"
          >
            ← All advisors
          </Link>
          <div className="mt-8 flex items-center gap-6">
            <div
              aria-hidden
              className="flex h-20 w-20 flex-none items-center justify-center rounded-full bg-primary text-2xl font-semibold text-white"
            >
              {initials}
            </div>
            <div>
              <h1 className="text-3xl font-semibold tracking-tight sm:text-4xl">
                {advisor.name}
              </h1>
              <p className="mt-1 text-muted">{advisor.role}</p>
            </div>
          </div>
        </Container>
      </section>

      {advisor.bio ? (
        <section className="py-16">
          <Container>
            <div
              className="prose max-w-3xl text-text/90 [&_p]:text-lg [&_p]:leading-relaxed [&_p]:mb-4"
              dangerouslySetInnerHTML={{ __html: advisor.bio }}
            />
          </Container>
        </section>
      ) : null}

      <section className="border-t border-border py-12">
        <Container>
          <p className="text-xs uppercase tracking-[0.2em] text-muted">
            Divisions
          </p>
          <div className="mt-4 flex flex-wrap gap-3">
            {divisions.length === 0 ? (
              <p className="text-sm text-muted">—</p>
            ) : (
              divisions.map((d) => (
                <Link
                  key={d.id}
                  href={`/divisions/${d.slug}`}
                  className="inline-flex items-center gap-2 rounded-full border border-border px-4 py-1.5 text-sm text-text hover:border-text transition-colors"
                >
                  {d.accent_color ? (
                    <span
                      aria-hidden
                      className="h-2 w-2 rounded-full"
                      style={{ background: d.accent_color }}
                    />
                  ) : null}
                  {d.title}
                </Link>
              ))
            )}
          </div>

          <div className="mt-10 flex flex-wrap gap-4 text-sm">
            {advisor.email ? (
              <a
                href={`mailto:${advisor.email}`}
                className="text-muted hover:text-text"
              >
                {advisor.email}
              </a>
            ) : null}
            {advisor.linkedin_url ? (
              <a
                href={advisor.linkedin_url}
                target="_blank"
                rel="noopener noreferrer"
                className="text-muted hover:text-text"
              >
                LinkedIn ↗
              </a>
            ) : null}
          </div>
        </Container>
      </section>
    </>
  );
}
